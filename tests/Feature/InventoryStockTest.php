<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Order;
use App\Models\RecipeIngredient;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Inventory\InventoryStockService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected User $user;

    protected Category $category;

    protected Item $item;

    protected Ingredient $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::create([
            'name' => 'Inv Restaurant',
            'slug' => 'inv-restaurant',
            'address' => '1 St',
            'is_active' => true,
        ]);

        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan-inv',
            'duration_in_days' => 365,
            'price' => 100,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addYear(),
            'amount_paid' => 100,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'notes' => null,
            'created_by' => null,
        ]);

        $this->user = User::create([
            'name' => 'Staff',
            'email' => 'staff@inv.com',
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
            'name' => 'Rice Bowl',
            'price' => 10,
            'is_available' => true,
        ]);

        $this->ingredient = Ingredient::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Rice',
            'unit' => 'kg',
            'low_stock_threshold' => 1,
            'track_expiry' => false,
            'is_active' => true,
        ]);

        RecipeIngredient::create([
            'item_id' => $this->item->id,
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 0.2,
        ]);
    }

    #[Test]
    public function stock_in_then_order_deducts_ingredient(): void
    {
        $table = RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'T1',
            'floor' => '1',
            'capacity' => 4,
            'status' => 'available',
        ]);

        $service = app(InventoryStockService::class);
        $branchId = $service->defaultBranchId($this->restaurant->id);

        $service->recordStockIn(
            $this->restaurant->id,
            $branchId,
            $this->ingredient->id,
            '10',
            null,
            'TEST-PO',
            null,
            $this->user->id,
            null
        );

        $this->assertSame('10.000000', $service->availableQuantity($this->ingredient->id, $branchId));

        $this->actingAs($this->user);

        $response = $this->post(route('orders.store'), [
            'order_type' => 'dine_in',
            'restaurant_table_id' => $table->id,
            'customer_name' => 'Walk-in',
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 2, 'unit_price' => 10],
            ],
        ]);

        $response->assertRedirect(route('pos'));

        $order = Order::where('restaurant_id', $this->restaurant->id)->latest()->first();
        $this->assertNotNull($order);

        // 2 bowls × 0.2 kg = 0.4 kg
        $after = $service->availableQuantity($this->ingredient->id, $branchId);
        $this->assertSame('9.600000', $after);
    }

    #[Test]
    public function inventory_dashboard_loads_for_restaurant_user(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('inventory.index'));
        $response->assertOk();
        $response->assertSee('Inventory');
    }
}
