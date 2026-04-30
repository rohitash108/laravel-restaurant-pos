<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Restaurant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    use ResolvesRestaurant;

    /**
     * Store Settings - Show
     */
    public function storeSettings()
    {
        $this->requirePermission('settings', 'view');
        $restaurantId = $this->currentRestaurantId();
        $restaurant = $restaurantId ? Restaurant::find($restaurantId) : null;
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'store') : [];

        return view('store-settings', compact('restaurant', 'settings'));
    }

    /**
     * Store Settings - Save
     */
    public function storeSettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('store-settings')->with('error', 'Restaurant not selected.');
        }

        $restaurant = Restaurant::findOrFail($restaurantId);

        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'payment_qr' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ], [
            'logo.image' => 'The logo must be an image (JPEG, PNG, GIF or WebP).',
            'logo.max' => 'The logo must not be larger than 5 MB.',
            'payment_qr.image' => 'Payment QR must be an image (JPEG, PNG, GIF or WebP).',
            'payment_qr.max' => 'Payment QR must not be larger than 2 MB.',
        ]);

        $logoPath = $restaurant->logo;

        if ($request->has('remove_logo') && $request->boolean('remove_logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = null;
        } elseif ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('restaurants', 'public');
        }

        $paymentQrPath = $restaurant->payment_qr;
        if ($request->has('remove_payment_qr') && $request->boolean('remove_payment_qr')) {
            if ($paymentQrPath && Storage::disk('public')->exists($paymentQrPath)) {
                Storage::disk('public')->delete($paymentQrPath);
            }
            $paymentQrPath = null;
        } elseif ($request->hasFile('payment_qr')) {
            if ($paymentQrPath && Storage::disk('public')->exists($paymentQrPath)) {
                Storage::disk('public')->delete($paymentQrPath);
            }
            $paymentQrPath = $request->file('payment_qr')->store('restaurants/payment-qr', 'public');
        }

        // Ensure currency never becomes null (DB column is NOT NULL)
        $currency = $request->input('currency');
        if (! $currency) {
            $currency = $restaurant->currency ?: 'INR';
        }

        // Update restaurant basic info
        $restaurant->update([
            'name' => $request->input('store_name', $restaurant->name),
            'address' => $request->input('address1', $restaurant->address),
            'address2' => $request->input('address2'),
            'country' => $request->input('country'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'pincode' => $request->input('pincode'),
            'email' => $request->input('email', $restaurant->email),
            'phone' => $request->input('phone', $restaurant->phone),
            'currency' => $currency,
            'gst_number' => $request->input('gst_number') ?: null,
            'logo' => $logoPath,
            'payment_qr' => $paymentQrPath,
        ]);

        // Save toggle settings
        $toggles = [
            'enable_qr_menu', 'enable_take_away', 'enable_dine_in',
            'enable_reservation', 'enable_order_via_qr', 'enable_delivery', 'enable_table',
        ];

        foreach ($toggles as $key) {
            Setting::setValue($restaurantId, 'store', $key, $request->has($key) ? '1' : '0');
        }

        return redirect()->route('store-settings')->with('success', 'Store settings updated successfully.');
    }

    /**
     * Payment Settings - Show
     */
    public function paymentSettings()
    {
        $restaurantId = $this->currentRestaurantId();
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'payment') : [];

        // Razorpay gateway creds (separate group, secret stays encrypted)
        $gateway = $restaurantId ? Setting::getGroup($restaurantId, 'payment_gateways') : [];
        $razorpayKeyId = $gateway['razorpay_key_id'] ?? '';
        $razorpaySecretSet = ! empty($gateway['razorpay_key_secret']);
        $razorpayEnabled = ($gateway['razorpay_enabled'] ?? '0') === '1';

        return view('payment-settings', compact(
            'settings',
            'razorpayKeyId',
            'razorpaySecretSet',
            'razorpayEnabled'
        ));
    }

    /**
     * Payment Settings - Save
     */
    public function paymentSettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('payment-settings')->with('error', 'Restaurant not selected.');
        }

        $request->validate([
            'razorpay_key_id'     => 'nullable|string|max:255',
            'razorpay_key_secret' => 'nullable|string|max:255',
        ]);

        $methods = ['cash', 'card', 'wallet', 'paypal', 'qr_reader', 'card_reader', 'bank'];
        foreach ($methods as $method) {
            Setting::setValue($restaurantId, 'payment', $method, $request->has($method) ? '1' : '0');
        }

        // Razorpay credentials (group: payment_gateways)
        Setting::setValue(
            $restaurantId,
            'payment_gateways',
            'razorpay_enabled',
            $request->has('razorpay_enabled') ? '1' : '0'
        );

        if ($request->filled('razorpay_key_id')) {
            Setting::setValue(
                $restaurantId,
                'payment_gateways',
                'razorpay_key_id',
                trim($request->input('razorpay_key_id'))
            );
        }

        // Only overwrite the secret if a new value was actually entered (so reloading the
        // page and saving doesn't blow away the stored credential).
        if ($request->filled('razorpay_key_secret')) {
            Setting::setValue(
                $restaurantId,
                'payment_gateways',
                'razorpay_key_secret',
                encrypt(trim($request->input('razorpay_key_secret')))
            );
        }

        return redirect()->route('payment-settings')->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Delivery Settings - Show
     */
    public function deliverySettings()
    {
        $restaurantId = $this->currentRestaurantId();
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'delivery') : [];

        return view('delivery-settings', compact('settings'));
    }

    /**
     * Delivery Settings - Save
     */
    public function deliverySettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('delivery-settings')->with('error', 'Restaurant not selected.');
        }

        $fields = [
            'free_delivery_enabled', 'free_delivery_over',
            'fixed_delivery_enabled', 'fixed_delivery_amount',
            'km_delivery_enabled', 'per_km_charge', 'minimum_delivery_over', 'min_distance_free_delivery',
        ];

        foreach ($fields as $field) {
            $value = $request->input($field);
            // For checkboxes, convert to 1/0
            if (in_array($field, ['free_delivery_enabled', 'fixed_delivery_enabled', 'km_delivery_enabled'])) {
                $value = $request->has($field) ? '1' : '0';
            }
            Setting::setValue($restaurantId, 'delivery', $field, $value);
        }

        return redirect()->route('delivery-settings')->with('success', 'Delivery settings updated successfully.');
    }

    /**
     * Print Settings - Show
     */
    public function printSettings()
    {
        $restaurantId = $this->currentRestaurantId();
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'print') : [];

        return view('print-settings', compact('settings'));
    }

    /**
     * Print Settings - Save
     */
    public function printSettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('print-settings')->with('error', 'Restaurant not selected.');
        }

        $toggles = ['enable_print', 'show_store_details', 'show_customer_details', 'show_notes', 'print_tokens'];
        foreach ($toggles as $key) {
            Setting::setValue($restaurantId, 'print', $key, $request->has($key) ? '1' : '0');
        }

        Setting::setValue($restaurantId, 'print', 'format', $request->input('format', ''));
        Setting::setValue($restaurantId, 'print', 'header', $request->input('header', ''));
        Setting::setValue($restaurantId, 'print', 'footer', $request->input('footer', ''));

        return redirect()->route('print-settings')->with('success', 'Print settings updated successfully.');
    }

    /**
     * Notifications Settings - Show
     */
    public function notificationsSettings()
    {
        $restaurantId = $this->currentRestaurantId();
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'notifications') : [];

        return view('notifications-settings', compact('settings'));
    }

    /**
     * Notifications Settings - Save
     */
    public function notificationsSettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('notifications-settings')->with('error', 'Restaurant not selected.');
        }

        $keys = [
            'mobile_push', 'desktop_notifications',
            'payment_push', 'payment_sms', 'payment_email',
            'transaction_push', 'transaction_sms', 'transaction_email',
            'activity_push', 'activity_sms', 'activity_email',
            'account_push', 'account_sms', 'account_email',
        ];

        foreach ($keys as $key) {
            Setting::setValue($restaurantId, 'notifications', $key, $request->has($key) ? '1' : '0');
        }

        return redirect()->route('notifications-settings')->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Integrations Settings - Show
     */
    public function integrationsSettings()
    {
        $restaurantId = $this->currentRestaurantId();
        $settings = $restaurantId ? Setting::getGroup($restaurantId, 'integrations') : [];

        return view('integrations-settings', compact('settings'));
    }

    /**
     * Integrations Settings - Save
     */
    public function integrationsSettingsUpdate(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('integrations-settings')->with('error', 'Restaurant not selected.');
        }

        $integrations = ['gmail', 'gupshup', 'printnode'];
        foreach ($integrations as $key) {
            Setting::setValue($restaurantId, 'integrations', $key, $request->has($key) ? '1' : '0');
        }

        return redirect()->route('integrations-settings')->with('success', 'Integration settings updated successfully.');
    }
}
