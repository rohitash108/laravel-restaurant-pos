<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Full flow E2E: Super Admin → create restaurant → Restaurant user adds category, item, table,
 * places POS order → complete order → check all reports → run through all restaurant modules.
 */
class FullFlowE2ETest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function full_flow_super_admin_creates_restaurant_then_restaurant_user_adds_data_places_order_and_all_reports_and_pages_work(): void
    {
        // ——— Step 1: Create Super Admin ———
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'restaurant_id' => null,
        ]);

        // ——— Step 2: Super Admin – Login & Dashboard ———
        $response = $this->post(route('login.submit'), [
            'email' => 'super@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('admin.dashboard'));

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Super Admin', false);

        // ——— Step 3: Super Admin – Create Restaurant (with admin user) ———
        $response = $this->post(route('admin.restaurants.store'), [
            'name' => 'Demo Restaurant',
            'slug' => 'demo-restaurant',
            'address' => '456 Demo St',
            'phone' => '+1 555 111 2222',
            'email' => 'demo@restaurant.com',
            'create_admin' => '1',
            'admin_name' => 'Restaurant Manager',
            'admin_email' => 'manager@demo.com',
            'admin_password' => 'password123',
            'admin_password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('admin.restaurants.index'));
        $response->assertSessionHas('success');

        $restaurant = Restaurant::where('slug', 'demo-restaurant')->first();
        $this->assertNotNull($restaurant, 'Restaurant should be created');
        $restaurantUser = User::where('email', 'manager@demo.com')->first();
        $this->assertNotNull($restaurantUser, 'Restaurant admin user should be created');
        $this->assertEquals($restaurant->id, $restaurantUser->restaurant_id);

        // ——— Step 4: Super Admin – Restaurants list, show, edit ———
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertStatus(200);
        $response->assertSee('Demo Restaurant', false);

        $response = $this->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);

        $response = $this->get(route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);

        // ——— Step 5: Logout Super Admin, Login as Restaurant User ———
        $this->post(route('logout'));
        $response = $this->post(route('login.submit'), [
            'email' => 'manager@demo.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('dashboard'));

        // ——— Step 6: Restaurant User – Dashboard ———
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard', false);

        // ——— Step 7: Add Category ———
        $response = $this->post(route('categories.store'), [
            'name' => 'Main Course',
        ]);
        $response->assertRedirect(route('categories'));
        $response->assertSessionHas('success');
        $category = Category::where('restaurant_id', $restaurant->id)->where('name', 'Main Course')->first();
        $this->assertNotNull($category);

        // ——— Step 8: Add Item (product) ———
        $response = $this->post(route('items.store'), [
            'name' => 'Grilled Chicken',
            'category_id' => $category->id,
            'price' => 12.99,
            'food_type' => 'non_veg',
        ]);
        $response->assertRedirect(route('items'));
        $response->assertSessionHas('success');
        $item = Item::where('restaurant_id', $restaurant->id)->where('name', 'Grilled Chicken')->first();
        $this->assertNotNull($item);

        // ——— Step 9: Add Table ———
        $response = $this->post(route('table.store'), [
            'name' => 'T1',
            'floor' => 'Ground',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $response->assertRedirect(route('table'));
        $response->assertSessionHas('success');
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)->where('name', 'T1')->first();
        $this->assertNotNull($table);

        // ——— Step 10: POS – Place Order (dine-in with table) ———
        $response = $this->post(route('orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'customer_name' => 'John Doe',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                    'unit_price' => 12.99,
                ],
            ],
        ]);
        $response->assertRedirect(route('pos'));
        $response->assertSessionHas('success');
        $order = Order::where('restaurant_id', $restaurant->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertCount(1, $order->items()->get());

        // ——— Step 11: Mark order completed (for reports) ———
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => Order::STATUS_COMPLETED,
        ]);
        $response->assertRedirect(route('orders'));
        $response->assertSessionHas('success');

        // ——— Step 12: All Reports ———
        $reportRoutes = [
            'earning-report' => url('earning-report'),
            'sales-report' => url('sales-report'),
            'order-report' => url('order-report'),
            'customer-report' => url('customer-report'),
            'audit-report' => url('audit-report'),
        ];
        foreach ($reportRoutes as $name => $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->status(), "Report {$name} should return 200");
        }

        // ——— Step 13: Invoices & Invoice details ———
        $response = $this->get(url('invoices'));
        $response->assertStatus(200);
        $response = $this->get(route('invoice-details', $order));
        $response->assertStatus(200);

        // ——— Step 14: All Restaurant modules (GET) ———
        $modules = [
            'pos' => route('pos'),
            'orders' => route('orders'),
            'kitchen' => url('kitchen'),
            'table' => url('table'),
            'reservations' => url('reservations'),
            'items' => url('items'),
            'categories' => url('categories'),
            'addons' => url('addons'),
            'customer' => url('customer'),
            'payments' => url('payments'),
            'coupons' => url('coupons'),
            'kanban-view' => url('kanban-view'),
            'users' => url('users'),
            'role-permission' => url('role-permission'),
            'store-settings' => url('store-settings'),
            'tax-settings' => url('tax-settings'),
            'payment-settings' => url('payment-settings'),
            'delivery-settings' => url('delivery-settings'),
            'print-settings' => url('print-settings'),
            'notifications-settings' => url('notifications-settings'),
            'integrations-settings' => url('integrations-settings'),
        ];
        foreach ($modules as $name => $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->status(), "Module {$name} should return 200");
        }

        // ——— Step 15: Add Reservation ———
        $response = $this->post(route('reservations.store'), [
            'customer_name' => 'Jane Smith',
            'customer_phone' => '555-999-0000',
            'reservation_date' => now()->addDays(2)->toDateString(),
            'reservation_time' => '19:00',
            'restaurant_table_id' => $table->id,
            'guests' => 2,
            'status' => 'booked',
        ]);
        $response->assertRedirect(route('reservations'));
        $reservation = Reservation::where('restaurant_id', $restaurant->id)->where('customer_name', 'Jane Smith')->first();
        $this->assertNotNull($reservation);

        // ——— Step 16: Categories update & Items update (smoke) ———
        $response = $this->put(route('categories.update', $category), [
            'name' => 'Main Courses',
        ]);
        $response->assertRedirect(route('categories'));
        $response->assertSessionHas('success');

        $response = $this->put(route('items.update', $item), [
            'name' => 'Grilled Chicken',
            'category_id' => $category->id,
            'price' => 13.99,
            'food_type' => 'non_veg',
        ]);
        $response->assertRedirect(route('items'));
        $response->assertSessionHas('success');

        // ——— Step 17: Logout ———
        $response = $this->post(route('logout'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function restaurant_user_can_place_takeaway_order_without_table(): void
    {
        $restaurant = Restaurant::create([
            'name' => 'Quick Bites',
            'slug' => 'quick-bites',
            'address' => '789 Street',
            'is_active' => true,
        ]);
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@quick.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $restaurant->id,
        ]);
        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Snacks',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $item = Item::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => 'Sandwich',
            'price' => 8.50,
            'is_available' => true,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('orders.store'), [
            'order_type' => 'takeaway',
            'restaurant_table_id' => '',
            'customer_name' => 'Walk-in',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1, 'unit_price' => 8.50],
            ],
        ]);
        $response->assertRedirect(route('pos'));
        $response->assertSessionHas('success');
        $this->assertEquals(1, Order::where('restaurant_id', $restaurant->id)->count());
    }
}
