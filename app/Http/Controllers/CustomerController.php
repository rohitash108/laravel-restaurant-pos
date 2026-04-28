<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Customer;
use App\Models\CustomerBalanceTransaction;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $customers = collect();
        $fromOrders = collect();

        if ($restaurantId) {
            // Order stats by customer_name + customer_phone (for merging)
            $orderStats = Order::where('restaurant_id', $restaurantId)
                ->selectRaw('customer_name, customer_phone, count(*) as orders_count, max(created_at) as last_order_at')
                ->groupBy('customer_name', 'customer_phone')
                ->get()
                ->keyBy(fn ($o) => trim($o->customer_name ?? '') . '|' . trim($o->customer_phone ?? ''));

            // Manually added customers (from customers table) with real order counts
            $customers = Customer::where('restaurant_id', $restaurantId)
                ->orderBy('name')
                ->get()
                ->map(function ($c) use ($orderStats) {
                    $c->customer_name = $c->name;
                    $c->customer_phone = $c->phone ?? null;
                    $key = trim($c->name ?? '') . '|' . trim($c->phone ?? '');
                    if ($orderStats->has($key)) {
                        $c->orders_count = (int) $orderStats[$key]->orders_count;
                        $c->last_order_at = $orderStats[$key]->last_order_at;
                    } else {
                        $c->orders_count = 0;
                        $c->last_order_at = null;
                    }
                    return $c;
                });

            // Customers derived from orders only (no row in customers table)
            $fromOrders = Order::where('restaurant_id', $restaurantId)
                ->selectRaw('customer_name, customer_phone, count(*) as orders_count, max(created_at) as last_order_at')
                ->groupBy('customer_name', 'customer_phone')
                ->havingRaw('(customer_name IS NOT NULL AND customer_name != "") OR (customer_phone IS NOT NULL AND customer_phone != "")')
                ->orderByDesc('orders_count')
                ->get();

            $existingKeys = $customers->map(fn ($c) => trim($c->customer_name ?? '') . '|' . trim($c->customer_phone ?? ''))->flip();
            foreach ($fromOrders as $o) {
                $key = trim($o->customer_name ?? '') . '|' . trim($o->customer_phone ?? '');
                if (! $existingKeys->has($key)) {
                    $customers->push($o);
                }
            }
            $customers = $customers->sortByDesc('orders_count')->values();
        }

        return view('customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('customer')->with('error', 'Restaurant not selected.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'status' => 'nullable|string|in:Active,Disabled',
        ]);

        $customer = Customer::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'phone' => $request->phone ?: null,
            'email' => $request->email ?: null,
            'date_of_birth' => $request->date_of_birth ?: null,
            'gender' => $request->gender ?: null,
            'status' => $request->status === 'Disabled' ? 'disabled' : 'active',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'customer' => ['id' => $customer->id, 'name' => $customer->name]]);
        }

        return redirect()->route('customer')->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $customer->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'status' => 'nullable|string|in:Active,Disabled',
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone ?: null,
            'email' => $request->email ?: null,
            'date_of_birth' => $request->date_of_birth ?: null,
            'gender' => $request->gender ?: null,
            'status' => $request->status === 'Disabled' ? 'disabled' : 'active',
        ]);

        return redirect()->route('customer')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $customer->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $customer->delete();

        return redirect()->route('customer')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Record a payment received from the customer (reduces amount due / increases balance).
     * E.g. customer owes 500, pays 100 → balance becomes -400 (due 400).
     */
    public function receivePayment(Request $request, Customer $customer)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $customer->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $amount = round((float) $request->amount, 2);
        $symbol = $customer->restaurant ? $customer->restaurant->currencySymbol() : '₹';
        $previousBalance = (float) $customer->balance;
        $customer->balance = $previousBalance + $amount;
        $customer->save();

        CustomerBalanceTransaction::create([
            'customer_id'   => $customer->id,
            'restaurant_id' => $restaurantId,
            'type'          => CustomerBalanceTransaction::TYPE_PAYMENT,
            'amount'        => $amount,
            'balance_after' => $customer->balance,
            'order_id'      => null,
            'user_id'       => auth()->id(),
            'notes'         => $request->notes,
        ]);

        $newBalance = (float) $customer->balance;
        $message = $newBalance >= 0
            ? 'Payment of ' . $symbol . number_format($amount, 2) . ' recorded. Customer balance: Credit ' . $symbol . number_format($newBalance, 2)
            : 'Payment of ' . $symbol . number_format($amount, 2) . ' recorded. Remaining due: ' . $symbol . number_format(-$newBalance, 2);

        return redirect()->route('customer')->with('success', $message);
    }
}
