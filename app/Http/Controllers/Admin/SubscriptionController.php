<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\SubscriptionBalanceTransaction;
use App\Models\SubscriptionPlan;
use App\Services\PlanItemSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    // ═══════════════════════════════════════════
    //  SUBSCRIPTION PLANS
    // ═══════════════════════════════════════════

    /**
     * List all subscription plans.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->latest()->get();

        return response()
            ->view('admin.subscription-plans', compact('plans'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Create a new subscription plan.
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        SubscriptionPlan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'duration_in_days' => $request->duration_in_days,
            'price' => $request->price,
            'credit_amount' => $request->filled('credit_amount') ? round((float) $request->credit_amount, 2) : null,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscription-plans')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Update an existing subscription plan.
     */
    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $plan->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'duration_in_days' => $request->duration_in_days,
            'price' => $request->price,
            'credit_amount' => $request->filled('credit_amount') ? round((float) $request->credit_amount, 2) : null,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscription-plans')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Delete a subscription plan.
     */
    public function destroyPlan(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscription-plans')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    // ═══════════════════════════════════════════
    //  SUBSCRIPTIONS
    // ═══════════════════════════════════════════

    /**
     * List subscriptions with filter support.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Subscription::with(['restaurant', 'plan', 'createdByUser'])->latest();

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'expired') {
            $query->expired();
        } elseif ($status === 'expiring') {
            $query->expiringSoon(7);
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        // Data for the "Assign Subscription" modal
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('name')->get();

        // Credit from expired subscriptions (overpayment) per restaurant — for "apply credit" on renew
        $restaurantCredits = Subscription::with('plan')
            ->whereIn('status', ['active', 'expired'])
            ->where('ends_at', '<', Carbon::today())
            ->get()
            ->groupBy('restaurant_id')
            ->map(function ($subs) {
                return $subs->sum(function ($s) {
                    $planPrice = (float) ($s->plan->price ?? 0);
                    $paid = (float) $s->amount_paid;
                    return max(0, round($paid - $planPrice, 2));
                });
            })
            ->toArray();

        // Stats
        $totalActive = Subscription::active()->count();
        $totalExpired = Subscription::expired()->count();
        $expiringSoon = Subscription::expiringSoon(7)->count();

        return response()
            ->view('admin.subscriptions', compact(
                'subscriptions', 'restaurants', 'plans', 'restaurantCredits',
                'status', 'totalActive', 'totalExpired', 'expiringSoon'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Assign or renew a subscription for a restaurant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'starts_at' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = $startsAt->copy()->addDays($plan->duration_in_days);

        // Prevent duplicate: if restaurant already has an active subscription overlapping this period, reject
        $existingActive = Subscription::where('restaurant_id', $request->restaurant_id)
            ->active()
            ->overlapping($startsAt, $endsAt)
            ->first();

        if ($existingActive) {
            return redirect()
                ->route('admin.subscriptions')
                ->with('error', 'Subscription already exists for this hotel for the selected period (active until ' . $existingActive->ends_at->format('d M Y') . '). Please choose a different start date or cancel the existing subscription first.')
                ->withInput($request->only(['restaurant_id', 'subscription_plan_id', 'starts_at', 'amount_paid', 'notes']));
        }

        // Mark any existing active subscriptions for this restaurant as expired (e.g. renewal after expiry)
        Subscription::where('restaurant_id', $request->restaurant_id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $initialBalance = $plan->credit_amount ? round((float) $plan->credit_amount, 2) : 0;

        $subscription = Subscription::create([
            'restaurant_id' => $request->restaurant_id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'amount_paid' => $request->amount_paid,
            'balance' => $initialBalance,
            'payment_method' => 'cash',
            'status' => 'active',
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        if ($initialBalance > 0) {
            SubscriptionBalanceTransaction::create([
                'subscription_id' => $subscription->id,
                'type' => SubscriptionBalanceTransaction::TYPE_CREDIT,
                'amount' => $initialBalance,
                'balance_after' => $initialBalance,
                'description' => 'Initial plan balance',
                'created_by' => auth()->id(),
            ]);
        }

        // Auto-assign all items bundled with this plan to the restaurant
        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $syncResult = app(PlanItemSyncService::class)->syncPlanToRestaurant($plan, $restaurant);
        $syncMsg = $syncResult['added'] > 0
            ? ' ' . $syncResult['added'] . ' menu item(s) auto-assigned from plan.'
            : '';

        return redirect()->route('admin.subscriptions')
            ->with('success', 'Subscription assigned successfully. Expires on ' . $endsAt->format('d M Y') . '.' . ($initialBalance > 0 ? ' Balance: ₹' . number_format($initialBalance, 2) . '.' : '') . $syncMsg);
    }

    /**
     * Record a payment toward subscription (increases amount_paid; for pending amount).
     */
    public function recordPayment(Request $request, Subscription $subscription)
    {
        $planPrice = (float) $subscription->plan->price;
        $currentPaid = (float) $subscription->amount_paid;

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $amount = round((float) $request->amount, 2);
        $newPaid = round($currentPaid + $amount, 2);
        $subscription->update(['amount_paid' => $newPaid]);

        $due = round($planPrice - $newPaid, 2);
        $credit = round($newPaid - $planPrice, 2);
        $message = 'Payment of ₹' . number_format($amount, 2) . ' recorded. Amount paid: ₹' . number_format($newPaid, 2);
        if ($due > 0) {
            $message .= '. Due: ₹' . number_format($due, 2);
        } elseif ($credit > 0) {
            $message .= '. Credit (advance): ₹' . number_format($credit, 2);
        } else {
            $message .= '. Fully paid.';
        }

        return redirect()->route('admin.subscriptions')
            ->with('success', $message);
    }

    /**
     * Cancel (delete) a subscription.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        return redirect()->route('admin.subscriptions')
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Balance history for a subscription (transactions list).
     */
    public function balanceHistory(Subscription $subscription)
    {
        $subscription->load(['restaurant', 'plan', 'balanceTransactions' => fn ($q) => $q->with('createdByUser')->latest()]);

        return response()
            ->view('admin.subscription-balance-history', compact('subscription'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Deduct from subscription balance (e.g. hotel paid 400, balance 500 -> 100).
     */
    public function debitBalance(Request $request, Subscription $subscription)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $amount = round((float) $request->amount, 2);
        $currentBalance = (float) $subscription->balance;
        if ($amount > $currentBalance) {
            return redirect()->back()
                ->with('error', 'Amount (₹' . number_format($amount, 2) . ') exceeds current balance (₹' . number_format($currentBalance, 2) . ').');
        }

        $balanceAfter = round($currentBalance - $amount, 2);
        $subscription->update(['balance' => $balanceAfter]);

        SubscriptionBalanceTransaction::create([
            'subscription_id' => $subscription->id,
            'type' => SubscriptionBalanceTransaction::TYPE_DEBIT,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $request->description ?: 'Balance used',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', '₹' . number_format($amount, 2) . ' deducted. Remaining balance: ₹' . number_format($balanceAfter, 2));
    }

    // ═══════════════════════════════════════════
    //  PLAN → ITEM MANAGEMENT
    // ═══════════════════════════════════════════

    /**
     * Show plan detail page with its current items and all available master items.
     */
    public function planItems(SubscriptionPlan $plan)
    {
        $plan->load('items');
        $allMasterItems = Item::master()->with('category')->orderBy('name')->get();

        return response()
            ->view('admin.plan-items', compact('plan', 'allMasterItems'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Sync item list for a plan (checkbox form POST).
     * After saving, propagate any newly-added items to active-plan restaurants.
     */
    public function syncPlanItems(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'item_ids'   => 'nullable|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        $newIds     = collect($request->input('item_ids', []))->map('intval');
        $currentIds = $plan->items()->pluck('items.id');

        $added   = $newIds->diff($currentIds);
        $removed = $currentIds->diff($newIds);

        $plan->items()->sync($newIds->all());

        $service     = app(PlanItemSyncService::class);
        $addedCount  = 0;
        $removedCount = 0;

        foreach ($added as $itemId) {
            $item = Item::find($itemId);
            if ($item) $addedCount += $service->propagateItemToActivePlanRestaurants($item, $plan);
        }

        foreach ($removed as $itemId) {
            $item = Item::find($itemId);
            if ($item) $removedCount += $service->retractItemFromPlanRestaurants($item, $plan);
        }

        $msg = 'Plan items updated.';
        if ($addedCount)   $msg .= " {$addedCount} assignment(s) added to active restaurants.";
        if ($removedCount) $msg .= " {$removedCount} plan-sourced assignment(s) removed.";

        return redirect()->route('admin.plan-items', $plan)->with('success', $msg);
    }

    /**
     * Force-push ALL plan items to every restaurant with an active subscription.
     * Useful for backfill after adding items to an existing plan.
     */
    public function forceSyncPlan(SubscriptionPlan $plan)
    {
        $result = app(PlanItemSyncService::class)->syncPlanToAllActiveRestaurants($plan);

        return redirect()->route('admin.plan-items', $plan)
            ->with('success', "Synced {$result['items_added']} item assignment(s) across {$result['restaurants']} restaurant(s).");
    }
}
