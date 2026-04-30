<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Restaurant;
use App\Services\RazorpayRouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Restaurant-side onboarding for Razorpay Route.
 *
 * The owner submits a single form: legal name, PAN, business type, bank
 * account, IFSC. We POST to Razorpay's Account/Stakeholder/Product APIs and
 * save the returned acc_XXX on the restaurant. From then on, payments for
 * this restaurant route through the platform's master account and Razorpay
 * automatically settles the merchant's share to their bank.
 */
class RazorpayRouteOnboardingController extends Controller
{
    use ResolvesRestaurant;

    public function show()
    {
        $this->requirePermission('settings', 'view');

        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('store-settings')->with('error', 'Restaurant not selected.');
        }

        $restaurant = Restaurant::findOrFail($restaurantId);

        return view('razorpay-route-onboarding', [
            'restaurant'      => $restaurant,
            'businessTypes'   => self::businessTypeOptions(),
            'platformFeePc'   => (float) config('services.razorpay.platform_fee_percent', 0),
            'masterConfigured' => filled(config('services.razorpay.master_key_id'))
                                 && filled(config('services.razorpay.master_key_secret')),
        ]);
    }

    public function store(Request $request, RazorpayRouteService $route)
    {
        $this->requirePermission('settings', 'update');

        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return back()->with('error', 'Restaurant not selected.');
        }
        $restaurant = Restaurant::findOrFail($restaurantId);

        $data = $request->validate([
            'legal_business_name' => 'required|string|max:255',
            'business_type'       => 'required|in:individual,proprietorship,partnership,private_limited,public_limited,llp,trust,society,ngo',
            'contact_name'        => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|min:10|max:15',
            'pan'                 => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'gst'                 => 'nullable|string|size:15',
            'street1'             => 'required|string|max:255',
            'street2'             => 'nullable|string|max:255',
            'city'                => 'required|string|max:100',
            'state'               => 'required|string|max:100',
            'postal_code'         => 'required|string|size:6',
            'bank_account_number' => 'required|string|min:8|max:20',
            'bank_ifsc'           => 'required|string|size:11|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'beneficiary_name'    => 'required|string|max:255',
            'category'            => 'nullable|string|max:50',
            'subcategory'         => 'nullable|string|max:50',
            'tnc_accepted'        => 'accepted',
        ]);

        try {
            // ── Step 1: create the linked account ────────────────────────
            $accountPayload = [
                'email'               => $data['email'],
                'phone'               => self::normalisePhone($data['phone']),
                'legal_business_name' => $data['legal_business_name'],
                'business_type'       => $data['business_type'],
                'contact_name'        => $data['contact_name'],
                'reference_id'        => 'restaurant_' . $restaurant->id,
                'profile' => [
                    'category'     => $data['category'] ?? 'food',
                    'subcategory'  => $data['subcategory'] ?? 'restaurant',
                    'addresses'    => [
                        'registered' => [
                            'street1'     => $data['street1'],
                            'street2'     => $data['street2'] ?? '',
                            'city'        => $data['city'],
                            'state'       => $data['state'],
                            'postal_code' => $data['postal_code'],
                            'country'     => 'IN',
                        ],
                    ],
                ],
                'legal_info' => array_filter([
                    'pan' => $data['pan'],
                    'gst' => $data['gst'] ?? null,
                ]),
                'notes' => [
                    'platform_restaurant_id' => (string) $restaurant->id,
                ],
            ];

            $account = $route->createLinkedAccount($accountPayload);
            $accountId = $account['id'] ?? null;
            if (! $accountId) {
                throw new RuntimeException('Razorpay did not return an account id.');
            }

            // ── Step 2: stakeholder ──────────────────────────────────────
            $stakeholderPayload = [
                'name'  => $data['contact_name'],
                'email' => $data['email'],
                'phone' => ['primary' => self::normalisePhone($data['phone'])],
                'kyc'   => ['pan' => $data['pan']],
                'addresses' => [
                    'residential' => [
                        'street'      => $data['street1'],
                        'city'        => $data['city'],
                        'state'       => $data['state'],
                        'postal_code' => $data['postal_code'],
                        'country'     => 'IN',
                    ],
                ],
            ];
            $stakeholder = $route->attachStakeholder($accountId, $stakeholderPayload);

            // ── Step 3: route product ────────────────────────────────────
            $product = $route->requestRouteProduct($accountId);
            $productId = $product['id'] ?? null;

            // ── Step 4: bank account ─────────────────────────────────────
            if ($productId) {
                $route->attachBankAccount($accountId, $productId, [
                    'account_number'   => $data['bank_account_number'],
                    'ifsc_code'        => strtoupper($data['bank_ifsc']),
                    'beneficiary_name' => $data['beneficiary_name'],
                ]);
            }

            // ── Persist on restaurant ────────────────────────────────────
            $restaurant->fill([
                'razorpay_linked_account_id'    => $accountId,
                'razorpay_account_status'       => $account['status'] ?? 'created',
                'razorpay_stakeholder_id'       => $stakeholder['id'] ?? null,
                'razorpay_settlement_account_id'=> $productId,
                'razorpay_business_type'        => $data['business_type'],
                'razorpay_kyc_data'             => $account,
            ])->save();

            return redirect()
                ->route('razorpay.route.onboarding')
                ->with('success', 'Account submitted. Razorpay typically verifies bank + PAN within 1–2 business days.');
        } catch (\Throwable $e) {
            Log::error('razorpay_route.onboarding_failed', [
                'restaurant_id' => $restaurant->id,
                'message'       => $e->getMessage(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Onboarding failed: ' . $e->getMessage());
        }
    }

    /** Re-pull current state from Razorpay (admin "Refresh status" button). */
    public function refresh(RazorpayRouteService $route)
    {
        $this->requirePermission('settings', 'update');

        $restaurantId = $this->currentRestaurantId();
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (! $restaurant->razorpay_linked_account_id) {
            return back()->with('error', 'No linked account on file.');
        }

        try {
            $account = $route->fetchAccount($restaurant->razorpay_linked_account_id);
            $route->syncRestaurantFromAccount($restaurant, $account);
            return back()->with('success', 'Status synced from Razorpay: ' . ($account['status'] ?? 'unknown'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public static function businessTypeOptions(): array
    {
        return [
            'individual'      => 'Individual',
            'proprietorship'  => 'Proprietorship',
            'partnership'     => 'Partnership',
            'private_limited' => 'Private Limited',
            'public_limited'  => 'Public Limited',
            'llp'             => 'LLP',
            'trust'           => 'Trust',
            'society'         => 'Society',
            'ngo'             => 'NGO',
        ];
    }

    private static function normalisePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);
        // Razorpay wants 10-digit Indian number (no country code)
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }
        return $digits;
    }
}
