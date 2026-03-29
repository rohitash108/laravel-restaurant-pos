<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $restaurantUser;

    protected User $superAdmin;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::create([
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'address' => '123 Test St',
            'phone' => '+1 555 000 0000',
            'email' => 'test@restaurant.com',
            'currency' => 'USD',
            'is_active' => true,
        ]);
        $this->restaurantUser = User::create([
            'name' => 'Restaurant Owner',
            'email' => 'restaurant@test.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'restaurant_id' => null,
        ]);
    }

    #[Test]
    public function guest_is_redirected_to_login_for_protected_routes(): void
    {
        $routes = [
            '/dashboard',
            '/pos',
            '/orders',
            '/kitchen',
            '/table',
            '/reservations',
            '/items',
            '/categories',
            '/addons',
            '/customer',
            '/earning-report',
            '/sales-report',
            '/order-report',
            '/customer-report',
            '/audit-report',
            '/invoices',
            '/users',
            '/role-permission',
            '/store-settings',
            '/tax-settings',
        ];
        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $this->assertTrue(
                $response->isRedirection() && $response->headers->get('Location') !== null,
                "Expected redirect from {$uri}"
            );
        }
    }

    #[Test]
    public function login_page_loads(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('login', false);
    }

    #[Test]
    public function restaurant_user_can_login_and_see_dashboard(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'restaurant@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));

        $this->actingAs($this->restaurantUser);
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard', false);
    }

    #[Test]
    public function restaurant_user_can_access_main_modules(): void
    {
        $this->actingAs($this->restaurantUser);

        $pages = [
            'dashboard' => route('dashboard'),
            'pos' => route('pos'),
            'orders' => route('orders'),
            'kitchen' => url('kitchen'),
            'table' => url('table'),
            'reservations' => url('reservations'),
            'items' => url('items'),
            'categories' => url('categories'),
            'addons' => url('addons'),
            'customer' => url('customer'),
            'earning-report' => url('earning-report'),
            'sales-report' => url('sales-report'),
            'order-report' => url('order-report'),
            'customer-report' => url('customer-report'),
            'audit-report' => url('audit-report'),
            'invoices' => url('invoices'),
            'users' => url('users'),
            'role-permission' => url('role-permission'),
            'store-settings' => url('store-settings'),
            'tax-settings' => url('tax-settings'),
            'payment-settings' => url('payment-settings'),
            'delivery-settings' => url('delivery-settings'),
            'print-settings' => url('print-settings'),
            'notifications-settings' => url('notifications-settings'),
            'integrations-settings' => url('integrations-settings'),
            'payments' => url('payments'),
            'coupons' => url('coupons'),
            'kanban-view' => url('kanban-view'),
        ];

        foreach ($pages as $name => $url) {
            $response = $this->get($url);
            $this->assertTrue(
                $response->status() === 200,
                "Module '{$name}' ({$url}) returned status {$response->status()}"
            );
        }
    }

    #[Test]
    public function super_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'superadmin@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
    }

    #[Test]
    public function super_admin_can_access_admin_modules(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Super Admin', false);

        $response = $this->get(route('admin.restaurants.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.restaurants.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function super_admin_can_view_and_update_account_profile(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Account', false);

        $response = $this->put(route('admin.profile.update'), [
            'name' => 'Super Admin Updated',
            'email' => 'superadmin@test.com',
        ]);
        $response->assertRedirect(route('admin.profile.edit'));

        $this->superAdmin->refresh();
        $this->assertSame('Super Admin Updated', $this->superAdmin->name);
    }

    #[Test]
    public function restaurant_user_cannot_access_super_admin_profile(): void
    {
        $this->actingAs($this->restaurantUser);
        $response = $this->get(route('admin.profile.edit'));
        $response->assertStatus(403);
    }

    #[Test]
    public function public_order_by_qr_route_accepts_valid_slug_and_table(): void
    {
        RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 1',
            'slug' => 'table-1',
            'floor' => '1st',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $response = $this->get('/order/test-restaurant/table-1');
        $response->assertStatus(200);
    }

    #[Test]
    public function register_page_loads(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    #[Test]
    public function logout_redirects_to_login(): void
    {
        $this->actingAs($this->restaurantUser);
        $response = $this->post(route('logout'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function restaurant_user_can_view_invoice_details_for_own_restaurant_order(): void
    {
        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'restaurant_table_id' => null,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_DINE_IN,
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 50,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 50,
            'customer_name' => 'Test Customer',
        ]);
        $this->actingAs($this->restaurantUser);
        $response = $this->get(route('invoice-details', $order));
        $response->assertStatus(200);
    }

    #[Test]
    public function restaurant_user_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->restaurantUser);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function restaurant_user_cannot_view_other_restaurant_invoice(): void
    {
        $otherRestaurant = Restaurant::create([
            'name' => 'Other Restaurant',
            'slug' => 'other-restaurant',
            'address' => 'Other St',
            'is_active' => true,
        ]);
        $order = Order::create([
            'restaurant_id' => $otherRestaurant->id,
            'restaurant_table_id' => null,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_TAKEAWAY,
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 30,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 30,
            'customer_name' => 'Other Customer',
        ]);
        $this->actingAs($this->restaurantUser);
        $response = $this->get(route('invoice-details', $order));
        $response->assertStatus(404);
    }

    #[Test]
    public function super_admin_can_update_restaurant(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->put(route('admin.restaurants.update', $this->restaurant), [
            'name' => 'Updated Restaurant Name',
            'address' => $this->restaurant->address,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.restaurants.index'));
        $response->assertSessionHas('success');
        $this->restaurant->refresh();
        $this->assertSame('Updated Restaurant Name', $this->restaurant->name);
    }

    #[Test]
    public function super_admin_can_delete_restaurant(): void
    {
        $toDelete = Restaurant::create([
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'address' => 'Addr',
            'is_active' => true,
        ]);
        $this->actingAs($this->superAdmin);
        $response = $this->delete(route('admin.restaurants.destroy', $toDelete));
        $response->assertRedirect(route('admin.restaurants.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('restaurants', ['id' => $toDelete->id]);
    }
}
