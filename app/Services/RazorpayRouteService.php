<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Handles Razorpay Route Linked Account onboarding for restaurants.
 *
 * Flow per restaurant:
 *   1. createLinkedAccount()      → POST /v2/accounts        → returns acc_XXX
 *   2. attachStakeholder()        → POST /v2/accounts/{acc}/stakeholders
 *   3. requestProductConfig()     → POST /v2/accounts/{acc}/products  (route product)
 *   4. attachBankAccount()        → PATCH the route product config with settlement.bank
 *   5. waitForActivation (async)  → webhook account.activated
 *
 * Docs:
 *   https://razorpay.com/docs/payments/route/onboard-linked-accounts/
 *   https://razorpay.com/docs/api/partners/account-onboarding/
 */
class RazorpayRouteService
{
    private const API_BASE = 'https://api.razorpay.com/v2';

    public function __construct(
        private readonly string $keyId,
        private readonly string $keySecret,
    ) {}

    public static function platform(): self
    {
        $keyId     = (string) config('services.razorpay.master_key_id', '');
        $keySecret = (string) config('services.razorpay.master_key_secret', '');

        if ($keyId === '' || $keySecret === '') {
            throw new RuntimeException(
                'Razorpay master keys are not configured. Set RAZORPAY_MASTER_KEY_ID and RAZORPAY_MASTER_KEY_SECRET in .env.'
            );
        }

        return new self($keyId, $keySecret);
    }

    /**
     * Step 1: Create the Linked Account itself.
     *
     * @param  array{
     *   email:string,
     *   phone:string,
     *   legal_business_name:string,
     *   business_type:string,
     *   contact_name:string,
     *   reference_id?:string,
     *   profile?:array,
     *   legal_info?:array,
     *   brand?:array,
     *   notes?:array
     * } $data
     * @return array<string,mixed>  raw Razorpay response (id, status, ...)
     */
    public function createLinkedAccount(array $data): array
    {
        return $this->call('POST', '/accounts', $data);
    }

    /**
     * Step 2: Attach a stakeholder (the human owner — required for KYC).
     *
     * @param  array{
     *   name:string,
     *   email:string,
     *   phone?:array{primary:string},
     *   addresses?:array,
     *   kyc?:array{pan?:string},
     *   notes?:array
     * } $data
     */
    public function attachStakeholder(string $accountId, array $data): array
    {
        return $this->call('POST', "/accounts/{$accountId}/stakeholders", $data);
    }

    /**
     * Step 3: Request the "route" product configuration on the linked account.
     * This is what tells Razorpay "this account should receive Route transfers".
     */
    public function requestRouteProduct(string $accountId, array $tnc = []): array
    {
        return $this->call('POST', "/accounts/{$accountId}/products", [
            'product_name' => 'route',
            'tnc_accepted' => $tnc['tnc_accepted'] ?? true,
            'ip'           => $tnc['ip'] ?? request()->ip(),
        ]);
    }

    /**
     * Step 4: Attach the settlement bank account to the route product.
     *
     * @param array{
     *   ifsc_code:string,
     *   beneficiary_name:string,
     *   account_type:string,
     *   account_number:string
     * } $bank
     */
    public function attachBankAccount(string $accountId, string $productId, array $bank): array
    {
        return $this->call('PATCH', "/accounts/{$accountId}/products/{$productId}", [
            'settlements' => [
                'account_number'   => $bank['account_number'],
                'ifsc_code'        => $bank['ifsc_code'],
                'beneficiary_name' => $bank['beneficiary_name'],
            ],
            'tnc_accepted' => true,
        ]);
    }

    /**
     * Fetch current state of a linked account (used by webhooks + admin re-sync).
     */
    public function fetchAccount(string $accountId): array
    {
        return $this->call('GET', "/accounts/{$accountId}");
    }

    /**
     * Validate a UPI VPA — useful when restaurant only gives us their VPA and
     * we want to confirm the registered name matches the PAN they typed.
     * (Different endpoint — uses v1 API, not v2.)
     */
    public function validateVpa(string $vpa): array
    {
        $res = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->acceptJson()
            ->asJson()
            ->post('https://api.razorpay.com/v1/payments/validate/vpa', [
                'vpa' => $vpa,
            ]);

        return $this->unwrap($res, 'POST', '/payments/validate/vpa', ['vpa' => $vpa]);
    }

    /**
     * Persist Razorpay's account state back onto the restaurant row.
     */
    public function syncRestaurantFromAccount(Restaurant $restaurant, array $razorpayAccount): void
    {
        $status = $razorpayAccount['status'] ?? null;

        $restaurant->fill([
            'razorpay_linked_account_id' => $razorpayAccount['id'] ?? $restaurant->razorpay_linked_account_id,
            'razorpay_account_status'    => $status,
            'razorpay_status_reason'     => $razorpayAccount['hold_funds']['reason'] ?? null,
            'razorpay_kyc_data'          => $razorpayAccount,
        ]);

        if ($status === 'activated' && empty($restaurant->razorpay_activated_at)) {
            $restaurant->razorpay_activated_at = now();
        }

        $restaurant->save();
    }

    // ─── Internals ──────────────────────────────────────────────────────────

    /**
     * Single guarded HTTP call to Razorpay API. Logs failures and throws a
     * clean RuntimeException carrying the Razorpay error description.
     */
    private function call(string $method, string $path, array $payload = []): array
    {
        $url = self::API_BASE . $path;

        $req = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->acceptJson()
            ->asJson()
            ->timeout(20);

        $response = match (strtoupper($method)) {
            'GET'    => $req->get($url, $payload),
            'POST'   => $req->post($url, $payload),
            'PATCH'  => $req->patch($url, $payload),
            'PUT'    => $req->put($url, $payload),
            'DELETE' => $req->delete($url, $payload),
            default  => throw new RuntimeException("Unsupported HTTP method {$method}"),
        };

        return $this->unwrap($response, $method, $path, $payload);
    }

    private function unwrap(Response $response, string $method, string $path, array $payload): array
    {
        $body = $response->json() ?? [];

        if ($response->failed()) {
            $err = $body['error']['description']
                ?? $body['error']['code']
                ?? ('Razorpay request failed with status ' . $response->status());

            Log::warning('razorpay_route.api_failure', [
                'method'   => $method,
                'path'     => $path,
                'status'   => $response->status(),
                'response' => $body,
                'payload'  => self::redact($payload),
            ]);

            throw new RuntimeException($err);
        }

        return is_array($body) ? $body : [];
    }

    /**
     * Strip account_number / pan from logged payloads so we don't leak PII to logs.
     */
    private static function redact(array $payload): array
    {
        $clone = $payload;
        if (isset($clone['settlements']['account_number'])) {
            $clone['settlements']['account_number'] = '***redacted***';
        }
        if (isset($clone['kyc']['pan'])) {
            $clone['kyc']['pan'] = '***redacted***';
        }
        return $clone;
    }
}
