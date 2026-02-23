<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrudModulesTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected User $user;

    protected Category $category;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::create([
            'name' => 'CRUD Restaurant',
            'slug' => 'crud-restaurant',
            'address' => '1 CRUD St',
            'is_active' => true,
        ]);
        $this->user = User::create([
            'name' => 'Staff',
            'email' => 'staff@crud.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Food',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $this->item = Item::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'Pizza',
            'price' => 15.00,
            'is_available' => true,
        ]);
    }

    // ——— Addons CRUD ———

    #[Test]
    public function addon_can_be_created(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('addons.store'), [
            'item_id' => $this->item->id,
            'addon_name' => 'Extra cheese',
            'price' => 2.50,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('addons'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('addons', [
            'restaurant_id' => $this->restaurant->id,
            'item_id' => $this->item->id,
            'addon_name' => 'Extra cheese',
        ]);
    }

    #[Test]
    public function addon_can_be_updated(): void
    {
        $addon = Addon::create([
            'restaurant_id' => $this->restaurant->id,
            'item_id' => $this->item->id,
            'addon_name' => 'Sauce',
            'price' => 1.00,
            'status' => 'active',
        ]);
        $this->actingAs($this->user);
        $response = $this->put(route('addons.update', $addon), [
            'item_id' => $this->item->id,
            'addon_name' => 'Spicy sauce',
            'price' => 1.50,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('addons'));
        $response->assertSessionHas('success');
        $addon->refresh();
        $this->assertSame('Spicy sauce', $addon->addon_name);
        $this->assertSame(1.5, (float) $addon->price);
    }

    #[Test]
    public function addon_can_be_deleted(): void
    {
        $addon = Addon::create([
            'restaurant_id' => $this->restaurant->id,
            'item_id' => $this->item->id,
            'addon_name' => 'Topping',
            'price' => 0.50,
            'status' => 'active',
        ]);
        $this->actingAs($this->user);
        $response = $this->delete(route('addons.destroy', $addon));
        $response->assertRedirect(route('addons'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('addons', ['id' => $addon->id]);
    }

    // ——— Item hide ———

    #[Test]
    public function item_hide_toggles_is_available(): void
    {
        $this->assertTrue($this->item->is_available);
        $this->actingAs($this->user);
        $response = $this->patch(route('items.hide', $this->item));
        $response->assertRedirect(route('items'));
        $response->assertSessionHas('success');
        $this->item->refresh();
        $this->assertFalse($this->item->is_available);

        $response = $this->patch(route('items.hide', $this->item));
        $response->assertRedirect(route('items'));
        $this->item->refresh();
        $this->assertTrue($this->item->is_available);
    }

    // ——— Customer: list shows balance for repeat customers ———

    #[Test]
    public function customer_page_shows_balance_for_customer_with_balance(): void
    {
        $customer = Customer::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Balance User',
            'phone' => '555-0000',
            'balance' => -25.50,
        ]);
        $this->actingAs($this->user);
        $response = $this->get(route('customer'));
        $response->assertStatus(200);
        $response->assertSee('Balance User', false);
        $response->assertSee('25.50', false);
    }

    #[Test]
    public function customer_can_be_created_and_updated(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('customer.store'), [
            'name' => 'New Customer',
            'phone' => '555-1111',
        ]);
        $response->assertRedirect(route('customer'));
        $response->assertSessionHas('success');
        $customer = Customer::where('restaurant_id', $this->restaurant->id)->where('name', 'New Customer')->first();
        $this->assertNotNull($customer);

        $response = $this->put(route('customer.update', $customer), [
            'name' => 'Updated Customer',
            'phone' => '555-2222',
        ]);
        $response->assertRedirect(route('customer'));
        $customer->refresh();
        $this->assertSame('Updated Customer', $customer->name);
    }

    // ——— Coupons CRUD ———

    #[Test]
    public function coupon_can_be_created(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('coupons.store'), [
            'code' => 'WELCOME10',
            'discount_type' => 'percentage',
            'discount_amount' => 10,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('coupons'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('coupons', [
            'restaurant_id' => $this->restaurant->id,
            'code' => 'WELCOME10',
        ]);
    }

    #[Test]
    public function coupon_can_be_updated_and_deleted(): void
    {
        $coupon = Coupon::create([
            'restaurant_id' => $this->restaurant->id,
            'code' => 'OLD',
            'discount_type' => 'fixed',
            'discount_amount' => 5,
            'is_active' => true,
        ]);
        $this->actingAs($this->user);
        $response = $this->put(route('coupons.update', $coupon), [
            'code' => 'NEWCODE',
            'discount_type' => 'percentage',
            'discount_amount' => 15,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('coupons'));
        $coupon->refresh();
        $this->assertSame('NEWCODE', $coupon->code);

        $response = $this->delete(route('coupons.destroy', $coupon));
        $response->assertRedirect(route('coupons'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    // ——— Table update & delete ———

    #[Test]
    public function table_can_be_updated_and_deleted(): void
    {
        $table = RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'T1',
            'slug' => 't1',
            'floor' => '1',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $this->actingAs($this->user);
        $response = $this->put(route('table.update', $table), [
            'name' => 'T1-Updated',
            'floor' => '1',
            'capacity' => 6,
            'status' => 'available',
        ]);
        $response->assertRedirect(route('table'));
        $response->assertSessionHas('success');
        $table->refresh();
        $this->assertSame('T1-Updated', $table->name);
        $this->assertSame(6, $table->capacity);

        $response = $this->delete(route('table.destroy', $table));
        $response->assertRedirect(route('table'));
        $this->assertDatabaseMissing('restaurant_tables', ['id' => $table->id]);
    }

    // ——— Reservation update & delete ———

    #[Test]
    public function reservation_can_be_updated_and_deleted(): void
    {
        $table = RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'T2',
            'slug' => 't2',
            'floor' => '1',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $reservation = Reservation::create([
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => 'Guest',
            'customer_phone' => '555-0000',
            'reservation_date' => now()->addDays(1),
            'reservation_time' => '19:00',
            'restaurant_table_id' => $table->id,
            'guests' => 2,
            'status' => 'booked',
        ]);
        $this->actingAs($this->user);
        $response = $this->put(route('reservations.update', $reservation), [
            'customer_name' => 'Guest Updated',
            'customer_phone' => '555-1111',
            'reservation_date' => $reservation->reservation_date->format('Y-m-d'),
            'reservation_time' => '20:00',
            'restaurant_table_id' => $table->id,
            'guests' => 3,
            'status' => 'booked',
        ]);
        $response->assertRedirect(route('reservations'));
        $reservation->refresh();
        $this->assertSame('Guest Updated', $reservation->customer_name);
        $this->assertSame(3, $reservation->guests);

        $response = $this->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('reservations'));
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    // ——— Settings POST (store-settings, tax-settings) ———

    #[Test]
    public function store_settings_can_be_updated(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('store-settings.update'), [
            'store_name' => $this->restaurant->name,
            'address1' => 'Updated Address',
            'currency' => 'INR',
        ]);
        $response->assertRedirect(route('store-settings'));
        $response->assertSessionHas('success');
        $this->restaurant->refresh();
        $this->assertSame('Updated Address', $this->restaurant->address);
    }

    #[Test]
    public function tax_setting_can_be_created(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('tax-settings.store'), [
            'name' => 'GST',
            'rate' => 18,
            'type' => 'exclusive',
        ]);
        $response->assertRedirect(route('tax-settings'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('taxes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'GST',
        ]);
    }

    // ——— Users CRUD ———

    #[Test]
    public function user_can_be_created_and_updated(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('users.store'), [
            'name' => 'New Waiter',
            'email' => 'waiter@crud.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'waiter',
        ]);
        $response->assertRedirect(route('users'));
        $response->assertSessionHas('success');
        $staff = User::where('restaurant_id', $this->restaurant->id)->where('email', 'waiter@crud.com')->first();
        $this->assertNotNull($staff);

        $response = $this->put(route('users.update', $staff), [
            'name' => 'Updated Waiter',
            'email' => 'waiter@crud.com',
            'role' => 'waiter',
        ]);
        $response->assertRedirect(route('users'));
        $staff->refresh();
        $this->assertSame('Updated Waiter', $staff->name);
    }

    // ——— Role & Permission CRUD ———

    #[Test]
    public function role_can_be_created(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('role-permission.store'), [
            'name' => 'Custom Role',
        ]);
        $response->assertRedirect(route('role-permission'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', [
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Custom Role',
        ]);
    }
}
