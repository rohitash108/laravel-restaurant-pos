<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Setting;
use Razorpay\Api\Api;

/**
 * Wraps the Razorpay PHP SDK so the rest of the app doesn't have to know
 * which mode (Route vs per-outlet) the restaurant is on.
 *
 * Two modes:
 *   - Route       : platform's MASTER keys are used; orders include a transfers[]
 *                   entry pointing at the restaurant's Linked Account (acc_XXX).
 *                   Razorpay automatically settles MDR + platform fee + merchant share.
 *   - Per-outlet  : restaurant's OWN keys (Setting::getValue 'payment_gateways'.razorpay_*)
 *                   — money goes straight to the restaurant's own Razorpay account.
 *
 * Selection logic (config 'services.razorpay.mode'):
 *   - 'route'      : Route only; if no linked account on restaurant → null (disabled)
 *   - 'per_outlet' : per-outlet only; ignore linked account
 *   - 'auto'       : Route if linked account is activated, else per-outlet, else null
 */
class RazorpayService
{
    public const MODE_ROUTE      = 'route';
    public const MODE_PER_OUTLET = 'per_outlet';

    private Api $api;

    public function __construct(
        private readonly string $keyId,
        string $keySecret,
        public readonly string $mode,
        public readonly ?string $linkedAccountId = null,
        public readonly float $platformFeePercent = 0.0,
    ) {
        $this->api = new Api($keyId, $keySecret);
    }

    /**
     * Resolve a configured RazorpayService for a given restaurant — or null
     * if Razorpay isn't usable for them (no Route account AND no per-outlet keys).
     */
    public static function forRestaurant(int $restaurantId): ?self
    {
        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            return null;
        }

        $configuredMode = (string) config('services.razorpay.mode', 'auto');

        // ── Route mode ──────────────────────────────────────────────────────
        if (in_array($configuredMode, ['route', 'auto'], true)
            && $restaurant->isRazorpayRouteActive()) {
            $masterKey    = (string) config('services.razorpay.master_key_id', '');
            $masterSecret = (string) config('services.razorpay.master_key_secret', '');
            if ($masterKey === '' || $masterSecret === '') {
                // Master keys missing — fall through to per-outlet if allowed
                if ($configuredMode === 'route') return null;
            } else {
                return new self(
                    keyId:              $masterKey,
                    keySecret:          $masterSecret,
                    mode:               self::MODE_ROUTE,
                    linkedAccountId:    $restaurant->razorpay_linked_account_id,
                    platformFeePercent: (float) ($restaurant->razorpay_platform_fee_percent
                        ?? config('services.razorpay.platform_fee_percent', 0)),
                );
            }
        }

        // ── Per-outlet mode ─────────────────────────────────────────────────
        if (in_array($configuredMode, ['per_outlet', 'auto'], true)) {
            $keyId           = Setting::getValue($restaurantId, 'payment_gateways', 'razorpay_key_id');
            $encryptedSecret = Setting::getValue($restaurantId, 'payment_gateways', 'razorpay_key_secret');
            $enabled         = Setting::getValue($restaurantId, 'payment_gateways', 'razorpay_enabled') === '1';

            if (! $enabled || ! $keyId || ! $encryptedSecret) {
                return null;
            }

            try {
                $keySecret = decrypt($encryptedSecret);
            } catch (\Exception) {
                return null;
            }

            return new self(
                keyId:     $keyId,
                keySecret: $keySecret,
                mode:      self::MODE_PER_OUTLET,
            );
        }

        return null;
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    public function isRoute(): bool
    {
        return $this->mode === self::MODE_ROUTE;
    }

    /**
     * Create a Razorpay order. In Route mode, automatically attaches a
     * transfers[] entry pointing at the linked account (so settlement is
     * split: MDR → Razorpay, platform fee → master account, rest → linked).
     *
     * @param  int    $amountPaise  Amount in paise (₹1 = 100 paise)
     * @param  string $receipt      Unique receipt reference
     * @return array{
     *   id: string, amount: int, currency: string,
     *   transfer_id?: ?string, platform_fee_paise?: int
     * }
     */
    public function createOrder(int $amountPaise, string $receipt): array
    {
        $payload = [
            'amount'          => $amountPaise,
            'currency'        => 'INR',
            'receipt'         => substr($receipt, 0, 40),
            'payment_capture' => 1,
        ];

        $platformFeePaise = 0;
        if ($this->isRoute() && $this->linkedAccountId) {
            $platformFeePaise = (int) round($amountPaise * ($this->platformFeePercent / 100));
            // Amount transferred to the merchant = total minus platform fee.
            // Razorpay's MDR is debited from the merchant's settlement separately.
            $merchantShare = max(0, $amountPaise - $platformFeePaise);

            $payload['transfers'] = [[
                'account'  => $this->linkedAccountId,
                'amount'   => $merchantShare,
                'currency' => 'INR',
                'notes'    => ['receipt' => substr($receipt, 0, 40)],
                'on_hold'  => 0,
            ]];
        }

        $order = $this->api->order->create($payload);

        // Pull the transfer id (if Route) so we can persist it on the order row.
        $transferId = null;
        if ($this->isRoute()) {
            try {
                $transfers = $this->api->order->fetch($order->id)->transfers();
                if (isset($transfers->items[0]->id)) {
                    $transferId = $transfers->items[0]->id;
                }
            } catch (\Throwable) {
                // Transfers list isn't critical; webhook will sync it.
            }
        }

        return [
            'id'                 => $order->id,
            'amount'             => $order->amount,
            'currency'           => $order->currency,
            'transfer_id'        => $transferId,
            'platform_fee_paise' => $platformFeePaise,
        ];
    }

    /**
     * Verify Razorpay payment signature.
     */
    public function verifyPayment(string $razorpayOrderId, string $razorpayPaymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature'  => $signature,
            ]);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
