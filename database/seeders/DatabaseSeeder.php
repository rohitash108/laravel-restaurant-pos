<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run after migrations: php artisan migrate --seed
     */
    public function run(): void
    {
        // Super Admin – manages restaurants only
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'restaurant_id' => null,
        ]);

        // Restaurant 1
        $restaurant = Restaurant::create([
            'name' => 'Taste of Lhasa',
            'slug' => 'taste-of-lhasa',
            'address' => '123 Main St',
            'phone' => '+1 555 123 4567',
            'email' => 'contact@tasteoflhasa.com',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        // Restaurant Owner (restaurant_admin) – manages products and menus
        User::create([
            'name' => 'Restaurant Owner',
            'email' => 'restaurant@example.com',
            'password' => Hash::make('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $restaurant->id,
        ]);

        // Tables
        $table1 = RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Table 1',
            'slug' => 'table-1',
            'floor' => '1st',
            'capacity' => 4,
            'status' => 'available',
        ]);
        RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Table 2',
            'slug' => 'table-2',
            'floor' => '1st',
            'capacity' => 4,
            'status' => 'available',
        ]);
        RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Table 3',
            'slug' => 'table-3',
            'floor' => '1st',
            'capacity' => 6,
            'status' => 'available',
        ]);

        // Demo categories and products (with images) – see DemoDataSeeder
        $this->call(DemoDataSeeder::class);

        // Sample order (for dashboard/POS) using first seeded items
        $items = Item::where('restaurant_id', $restaurant->id)->get();
        $dish1 = $items->first();
        $dish2 = $items->skip(1)->first();
        $drink = $items->where('category_id', Category::where('restaurant_id', $restaurant->id)->where('name', 'Drinks')->value('id'))->first() ?? $items->last();
        if ($dish1 && $drink) {
            $subtotal = $dish1->price + ($drink->price * 2);
            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'restaurant_table_id' => $table1->id,
                'order_number' => Order::generateOrderNumber(),
                'order_type' => Order::TYPE_QR_ORDER,
                'status' => Order::STATUS_COMPLETED,
                'subtotal' => (float) $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => (float) $subtotal,
                'customer_name' => 'John Doe',
                'notes' => null,
            ]);
            $order->items()->create([
                'item_id' => $dish1->id,
                'item_name' => $dish1->name,
                'unit_price' => $dish1->price,
                'quantity' => 1,
                'total_price' => $dish1->price,
                'notes' => null,
            ]);
            $order->items()->create([
                'item_id' => $drink->id,
                'item_name' => $drink->name,
                'unit_price' => $drink->price,
                'quantity' => 2,
                'total_price' => $drink->price * 2,
                'notes' => null,
            ]);
        }
    }
}
