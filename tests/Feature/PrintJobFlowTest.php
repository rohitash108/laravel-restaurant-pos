<?php

namespace Tests\Feature;

use App\Models\PrintJob;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifies the Laravel print-job queue (enqueue → poll next → mark printed).
 * Bluetooth / ESC/POS happens in a separate client (e.g. Android); this app does not see the printer.
 */
class PrintJobFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::create([
            'name' => 'Print Test Restaurant',
            'slug' => 'print-test-restaurant',
            'address' => '1 Print St',
            'is_active' => true,
        ]);

        $plan = SubscriptionPlan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan-print',
            'duration_in_days' => 30,
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
            'name' => 'Print Staff',
            'email' => 'printstaff@test.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    #[Test]
    public function enqueue_then_next_claims_job_then_mark_printed_succeeds(): void
    {
        $this->actingAs($this->user);

        $enqueue = $this->postJson(route('print-jobs.enqueue'), [
            'type' => 'receipt',
            'payload' => ['demo' => true, 'lines' => ['Test line']],
        ]);
        $enqueue->assertOk();
        $enqueue->assertJson(['success' => true]);
        $jobId = $enqueue->json('job_id');
        $this->assertNotNull($jobId);

        $next = $this->getJson(route('print-jobs.next'));
        $next->assertOk();
        $next->assertJsonPath('job.id', $jobId);
        $next->assertJsonPath('job.type', 'receipt');
        $next->assertJsonPath('job.payload.demo', true);

        $job = PrintJob::find($jobId);
        $this->assertNotNull($job);
        $this->assertSame('claimed', $job->status);
        $this->assertNotNull($job->claimed_at);

        $printed = $this->postJson(route('print-jobs.printed', $job));
        $printed->assertOk();
        $printed->assertJson(['success' => true]);

        $job->refresh();
        $this->assertSame('printed', $job->status);
        $this->assertNotNull($job->printed_at);
    }

    #[Test]
    public function next_returns_null_when_no_pending_jobs(): void
    {
        $this->actingAs($this->user);

        $next = $this->getJson(route('print-jobs.next'));
        $next->assertOk();
        $next->assertJson(['job' => null]);
    }
}
