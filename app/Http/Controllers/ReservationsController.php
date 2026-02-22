<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard');
        }

        $reservations = Reservation::where('restaurant_id', $restaurantId)
            ->with('table')
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        $tables = RestaurantTable::where('restaurant_id', $restaurantId)->orderBy('name')->get();

        return view('reservations', compact('reservations', 'tables'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'Restaurant not selected.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'reservation_date' => ['required', 'date'],
            'reservation_time' => ['nullable', 'string', 'max:20'],
            'restaurant_table_id' => [
                'nullable',
                'exists:restaurant_tables,id',
                function ($attr, $value, $fail) use ($restaurantId, $request) {
                    if (! $value) {
                        return;
                    }
                    if (RestaurantTable::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                        $fail('The selected table does not belong to your restaurant.');
                        return;
                    }
                    $date = $request->input('reservation_date');
                    $time = $request->input('reservation_time');
                    $time = ($time === null || $time === '') ? null : $time;
                    $alreadyBooked = Reservation::where('restaurant_table_id', $value)
                        ->where('reservation_date', $date)
                        ->where('status', 'booked')
                        ->where(function ($q) use ($time) {
                            if ($time === null) {
                                $q->where(function ($q2) {
                                    $q2->whereNull('reservation_time')->orWhere('reservation_time', '');
                                });
                            } else {
                                $q->where('reservation_time', $time);
                            }
                        })
                        ->exists();
                    if ($alreadyBooked) {
                        $fail('This table is already booked for the selected date and time.');
                    }
                },
            ],
            'guests' => ['required', 'integer', 'min:1', 'max:99'],
            'status' => ['required', 'string', 'in:booked,cancelled,completed,no-show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'customer_name.required' => 'Customer name is required.',
            'reservation_date.required' => 'Reservation date is required.',
            'guests.required' => 'Number of guests is required.',
            'status.required' => 'Status is required.',
        ]);

        $validated['restaurant_id'] = $restaurantId;

        Reservation::create($validated);

        return redirect()->route('reservations')->with('success', 'Reservation created successfully.');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $reservation->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'reservation_date' => ['required', 'date'],
            'reservation_time' => ['nullable', 'string', 'max:20'],
            'restaurant_table_id' => [
                'nullable',
                'exists:restaurant_tables,id',
                function ($attr, $value, $fail) use ($restaurantId, $request, $reservation) {
                    if (! $value) {
                        return;
                    }
                    if (RestaurantTable::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                        $fail('The selected table does not belong to your restaurant.');
                        return;
                    }
                    $date = $request->input('reservation_date');
                    $time = $request->input('reservation_time');
                    $time = ($time === null || $time === '') ? null : $time;
                    $alreadyBooked = Reservation::where('restaurant_table_id', $value)
                        ->where('reservation_date', $date)
                        ->where('status', 'booked')
                        ->where('id', '!=', $reservation->id)
                        ->where(function ($q) use ($time) {
                            if ($time === null) {
                                $q->where(function ($q2) {
                                    $q2->whereNull('reservation_time')->orWhere('reservation_time', '');
                                });
                            } else {
                                $q->where('reservation_time', $time);
                            }
                        })
                        ->exists();
                    if ($alreadyBooked) {
                        $fail('This table is already booked for the selected date and time.');
                    }
                },
            ],
            'guests' => ['required', 'integer', 'min:1', 'max:99'],
            'status' => ['required', 'string', 'in:booked,cancelled,completed,no-show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $reservation->update($validated);

        return redirect()->route('reservations')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $reservation->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $reservation->delete();

        return redirect()->route('reservations')->with('success', 'Reservation deleted successfully.');
    }
}
