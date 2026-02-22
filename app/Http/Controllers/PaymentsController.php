<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;

class PaymentsController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $payments = $restaurantId
            ? Order::where('restaurant_id', $restaurantId)->where('status', 'completed')->with(['table', 'items'])->latest()->paginate(15)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('payments', compact('payments'));
    }
}
