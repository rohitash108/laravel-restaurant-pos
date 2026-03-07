<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\SubscriptionBalanceTransaction;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionPlanSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'restaurant_id' => null,
        ]);
        $this->restaurant = Restaurant::create([
            'name' => 'Test Hotel',
            'slug' => 'test-hotel',
            'address' => '1 Test St',
            'currency' => 'INR',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function step1_plans_page_loads_for_super_admin(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.subscription-plans'));
        $response->assertStatus(200);
        $response->assertSee('Subscription');
    }

    #[Test]
    public function step2_create_subscription_plan_succeeds(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscription-plans.store'), [
            'name' => 'Monthly Plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => 100,
            'description' => 'Monthly subscription',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.subscription-plans'));
        $response->assertSessionHas('success');

        $plan = SubscriptionPlan::where('name', 'Monthly Plan')->first();
        $this->assertNotNull($plan);
        $this->assertSame(30, $plan->duration_in_days);
        $this->assertSame('500.00', (string) $plan->price);
        $this->assertSame('100.00', (string) $plan->credit_amount);
        $this->assertTrue($plan->is_active);
    }

    #[Test]
    public function step3_update_subscription_plan_succeeds(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
            'duration_in_days' => 30,
            'price' => 300,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->put(route('admin.subscription-plans.update', $plan), [
            'name' => 'Updated Monthly',
            'duration_in_days' => 30,
            'price' => 400,
            'credit_amount' => 50,
            'description' => null,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('admin.subscription-plans'));
        $response->assertSessionHas('success');

        $plan->refresh();
        $this->assertSame('Updated Monthly', $plan->name);
        $this->assertSame('400.00', (string) $plan->price);
        $this->assertSame('50.00', (string) $plan->credit_amount);
    }

    #[Test]
    public function step4_subscriptions_page_loads_and_assign_modal_data_present(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.subscriptions'));
        $response->assertStatus(200);
        $response->assertSee('Subscriptions');
        $response->assertSee('Assign Subscription');
        $response->assertSee($this->restaurant->name);
    }

    #[Test]
    public function step5_assign_subscription_succeeds_and_sets_balance(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Yearly',
            'slug' => 'yearly',
            'duration_in_days' => 60,
            'price' => 500,
            'credit_amount' => 200,
            'is_active' => true,
        ]);

        $startsAt = Carbon::today()->format('Y-m-d');

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.store'), [
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'amount_paid' => 500,
            'notes' => 'First subscription',
        ]);
        $response->assertRedirect(route('admin.subscriptions'));
        $response->assertSessionHas('success');

        $sub = Subscription::where('restaurant_id', $this->restaurant->id)->first();
        $this->assertNotNull($sub);
        $this->assertSame('active', $sub->status);
        $this->assertSame('500.00', (string) $sub->amount_paid);
        $this->assertSame('200.00', (string) $sub->balance);

        $txn = SubscriptionBalanceTransaction::where('subscription_id', $sub->id)->first();
        $this->assertNotNull($txn);
        $this->assertSame(SubscriptionBalanceTransaction::TYPE_CREDIT, $txn->type);
        $this->assertSame('200.00', (string) $txn->amount);
    }

    #[Test]
    public function step6_assign_again_overlapping_shows_error_and_does_not_create_second(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Monthly',
            'slug' => 'monthly',
            'duration_in_days' => 30,
            'price' => 300,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $startsAt = Carbon::today()->format('Y-m-d');

        Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 300,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $countBefore = Subscription::where('restaurant_id', $this->restaurant->id)->count();

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.store'), [
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'amount_paid' => 300,
            'notes' => 'Duplicate attempt',
        ]);
        $response->assertRedirect(route('admin.subscriptions'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('already exists', session('error'));

        $countAfter = Subscription::where('restaurant_id', $this->restaurant->id)->count();
        $this->assertSame($countBefore, $countAfter);
    }

    #[Test]
    public function step7_assign_after_expiry_succeeds_and_expires_old(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Monthly',
            'slug' => 'monthly',
            'duration_in_days' => 30,
            'price' => 300,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $oldStart = Carbon::today()->subDays(60);
        $oldEnd = Carbon::today()->subDays(31);

        $oldSub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $oldStart,
            'ends_at' => $oldEnd,
            'amount_paid' => 300,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $newStartsAt = Carbon::today()->format('Y-m-d');

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.store'), [
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $newStartsAt,
            'amount_paid' => 300,
            'notes' => 'Renewal',
        ]);
        $response->assertRedirect(route('admin.subscriptions'));
        $response->assertSessionHas('success');

        $oldSub->refresh();
        $this->assertSame('expired', $oldSub->status);

        $newSub = Subscription::where('restaurant_id', $this->restaurant->id)
            ->where('id', '!=', $oldSub->id)
            ->first();
        $this->assertNotNull($newSub);
        $this->assertSame('active', $newSub->status);
    }

    #[Test]
    public function step8_balance_history_page_loads(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => 100,
            'is_active' => true,
        ]);

        $sub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 500,
            'balance' => 100,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.subscriptions.balance-history', $sub));
        $response->assertStatus(200);
        $response->assertSee('Balance');
        $response->assertSee('100');
    }

    #[Test]
    public function step9_debit_balance_reduces_balance_and_creates_transaction(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => 100,
            'is_active' => true,
        ]);

        $sub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 500,
            'balance' => 100,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.debit-balance', $sub), [
            'amount' => 40,
            'description' => 'Usage charge',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertSame('60.00', (string) $sub->balance);

        $debit = SubscriptionBalanceTransaction::where('subscription_id', $sub->id)
            ->where('type', SubscriptionBalanceTransaction::TYPE_DEBIT)
            ->first();
        $this->assertNotNull($debit);
        $this->assertSame('40.00', (string) $debit->amount);
        $this->assertSame('60.00', (string) $debit->balance_after);
    }

    #[Test]
    public function step10_debit_more_than_balance_returns_error(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => 50,
            'is_active' => true,
        ]);

        $sub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 500,
            'balance' => 50,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.debit-balance', $sub), [
            'amount' => 100,
            'description' => 'Too much',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('exceeds', session('error'));

        $sub->refresh();
        $this->assertSame('50.00', (string) $sub->balance);
    }

    #[Test]
    public function step11_cancel_subscription_sets_status_to_cancelled(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $sub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 500,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->delete(route('admin.subscriptions.destroy', $sub));
        $response->assertRedirect(route('admin.subscriptions'));
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertSame('cancelled', $sub->status);
    }

    #[Test]
    public function step13_expired_subscription_user_cannot_login_sees_message(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        // Subscription that has already ended
        Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today()->subDays(60),
            'ends_at' => Carbon::today()->subDays(31),
            'amount_paid' => 500,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $hotelUser = User::create([
            'name' => 'Hotel Staff',
            'email' => 'hotel@expired.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'hotel@expired.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('subscription is expired', session('error'));
        $this->assertGuest();
    }

    #[Test]
    public function step14_active_subscription_user_can_login(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => null,
            'is_active' => true,
        ]);
        Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 500,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $hotelUser = User::create([
            'name' => 'Hotel Staff',
            'email' => 'hotel@active.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'hotel@active.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($hotelUser);
    }

    #[Test]
    public function step15_record_payment_increases_amount_paid_and_reduces_pending(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Plan',
            'slug' => 'plan',
            'duration_in_days' => 30,
            'price' => 500,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $sub = Subscription::create([
            'restaurant_id' => $this->restaurant->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(30),
            'amount_paid' => 200,
            'balance' => 0,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->superAdmin->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.subscriptions.record-payment', $sub), [
            'amount' => 150,
            'notes' => 'Second instalment',
        ]);
        $response->assertRedirect(route('admin.subscriptions'));
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertSame('350.00', (string) $sub->amount_paid);
    }

    #[Test]
    public function step12_destroy_plan_succeeds(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'duration_in_days' => 30,
            'price' => 100,
            'credit_amount' => null,
            'is_active' => true,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->delete(route('admin.subscription-plans.destroy', $plan));
        $response->assertRedirect(route('admin.subscription-plans'));
        $response->assertSessionHas('success');

        $this->assertNull(SubscriptionPlan::find($plan->id));
    }
}
