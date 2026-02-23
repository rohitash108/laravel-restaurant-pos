<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PosOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected User $user;

    protected Category $category;

    protected Item $item;

    protected RestaurantTable $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::create([
            'name' => 'Flow Restaurant',
            'slug' => 'flow-restaurant',
            'address' => '1 Flow St',
            'is_active' => true,
        ]);
        $this->user = User::create([
            'name' => 'Staff',
            'email' => 'staff@flow.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->category = Category::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Mains',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $this->item = Item::create([
            'restaurant_id' => $this->restaurant->id,
            'category_id' => $this->category->id,
            'name' => 'Burger',
            'price' => 10.00,
            'is_available' => true,
        ]);
        $this->table = RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'T1',
            'floor' => '1',
            'capacity' => 4,
            'status' => 'available',
        ]);
    }

    #[Test]
    public function order_store_accepts_discount_amount_and_received_amount(): void
    {
        $this->actingAs($this->user);
        $response = $this->post(route('orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $this->table->id,
            'customer_name' => 'Walk-in',
            'discount_amount' => 2.00,
            'received_amount' => 15.00,
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 2, 'unit_price' => 10.00],
            ],
        ]);
        $response->assertRedirect(route('pos'));
        $response->assertSessionHas('success');

        $order = Order::where('restaurant_id', $this->restaurant->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertSame(2.0, (float) $order->discount_amount);
        $this->assertSame(15.0, (float) $order->received_amount);
        $this->assertSame(20.0, (float) $order->subtotal);
        $this->assertGreaterThan(0, (float) $order->tax_amount);
        $this->assertSame(round(20 + (float) $order->tax_amount - 2, 2), (float) $order->total);
    }

    #[Test]
    public function order_store_with_coupon_applies_discount_and_stores_coupon_id(): void
    {
        $coupon = Coupon::create([
            'restaurant_id' => $this->restaurant->id,
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_amount' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($this->user);
        $response = $this->post(route('orders.store'), [
            'order_type' => 'takeaway',
            'customer_name' => 'Walk-in',
            'coupon_id' => $coupon->id,
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 1, 'unit_price' => 10.00],
            ],
        ]);
        $response->assertRedirect(route('pos'));
        $response->assertSessionHas('success');

        $order = Order::where('restaurant_id', $this->restaurant->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertSame((int) $coupon->id, (int) $order->coupon_id);
        $this->assertSame(10.0, (float) $order->subtotal);
        $this->assertSame(1.0, (float) $order->discount_amount);
        $this->assertGreaterThan(0, (float) $order->tax_amount);
    }

    #[Test]
    public function order_store_with_customer_id_updates_customer_balance(): void
    {
        $customer = Customer::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Jane Doe',
            'phone' => '555-1234',
            'balance' => 0,
        ]);

        $this->actingAs($this->user);
        $response = $this->post(route('orders.store'), [
            'order_type' => 'takeaway',
            'customer_name' => $customer->name,
            'customer_id' => $customer->id,
            'discount_amount' => 0,
            'received_amount' => 5.00,
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 1, 'unit_price' => 10.00],
            ],
        ]);
        $response->assertRedirect(route('pos'));

        $order = Order::where('restaurant_id', $this->restaurant->id)->latest()->first();
        $this->assertNotNull($order);
        $total = (float) $order->total;
        $received = 5.0;
        $unpaid = $total - $received;

        $customer->refresh();
        $this->assertSame(round(-$unpaid, 2), round((float) $customer->balance, 2));
    }

    #[Test]
    public function pos_edit_order_page_loads_for_editable_order(): void
    {
        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'restaurant_table_id' => $this->table->id,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_DINE_IN,
            'status' => Order::STATUS_PENDING,
            'subtotal' => 10,
            'tax_amount' => 1.80,
            'discount_amount' => 0,
            'total' => 11.80,
            'customer_name' => 'Test',
        ]);

        $this->actingAs($this->user);
        $response = $this->get(route('orders.edit', $order));
        $response->assertStatus(200);
    }

    #[Test]
    public function order_update_changes_items_and_totals(): void
    {
        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'restaurant_table_id' => $this->table->id,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_DINE_IN,
            'status' => Order::STATUS_PENDING,
            'subtotal' => 10,
            'tax_amount' => 1.80,
            'discount_amount' => 0,
            'total' => 11.80,
            'customer_name' => 'Test',
        ]);
        $order->items()->create([
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'unit_price' => 10,
            'quantity' => 1,
            'total_price' => 10,
        ]);

        $this->actingAs($this->user);
        $response = $this->put(route('orders.update', $order), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $this->table->id,
            'customer_name' => 'Updated',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 3, 'unit_price' => 10.00],
            ],
        ]);
        $response->assertRedirect(route('orders'));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame(30.0, (float) $order->subtotal);
        $this->assertCount(1, $order->items()->get());
        $this->assertSame(3, $order->items()->first()->quantity);
    }

    #[Test]
    public function order_status_can_be_updated(): void
    {
        $order = Order::create([
            'restaurant_id' => $this->restaurant->id,
            'restaurant_table_id' => $this->table->id,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_DINE_IN,
            'status' => Order::STATUS_PENDING,
            'subtotal' => 10,
            'tax_amount' => 1.80,
            'discount_amount' => 0,
            'total' => 11.80,
            'customer_name' => 'Test',
        ]);

        $this->actingAs($this->user);
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => Order::STATUS_COMPLETED,
        ]);
        $response->assertRedirect(route('orders'));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame(Order::STATUS_COMPLETED, $order->status);
    }
}
