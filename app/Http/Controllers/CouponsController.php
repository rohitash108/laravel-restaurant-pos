<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard');
        }

        $coupons = Coupon::where('restaurant_id', $restaurantId)
            ->with('category')
            ->orderBy('code')
            ->get();

        $categories = Category::where('restaurant_id', $restaurantId)->orderBy('name')->get();

        return view('coupons', compact('coupons', 'categories'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'Restaurant not selected.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64', 'unique:coupons,code,NULL,id,restaurant_id,' . $restaurantId],
            'category_id' => ['nullable', 'exists:categories,id', function ($attr, $value, $fail) use ($restaurantId) {
                if ($value && Category::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected category does not belong to your restaurant.');
                }
            }],
            'discount_type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists for your restaurant.',
            'discount_type.required' => 'Discount type is required.',
            'discount_amount.required' => 'Discount amount is required.',
        ]);

        $validated['restaurant_id'] = $restaurantId;
        $validated['is_active'] = $request->boolean('is_active', true);

        Coupon::create($validated);

        return redirect()->route('coupons')->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $coupon->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64', 'unique:coupons,code,' . $coupon->id . ',id,restaurant_id,' . $restaurantId],
            'category_id' => ['nullable', 'exists:categories,id', function ($attr, $value, $fail) use ($restaurantId) {
                if ($value && Category::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected category does not belong to your restaurant.');
                }
            }],
            'discount_type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $coupon->update($validated);

        return redirect()->route('coupons')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $coupon->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $coupon->delete();

        return redirect()->route('coupons')->with('success', 'Coupon deleted successfully.');
    }
}
