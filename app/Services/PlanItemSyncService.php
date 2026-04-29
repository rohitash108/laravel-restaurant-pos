<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\RestaurantItemAssignment;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for syncing plan items ↔ restaurants.
 *
 * Rules:
 *  - source='plan'   → created by this service; removable on plan change
 *  - source='manual' → created by super admin directly; never auto-removed
 *
 * Duplicate prevention: every upsert uses (restaurant_id, item_id) unique key.
 */
class PlanItemSyncService
{
    // ──────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Assign all plan items to a restaurant.
     * Called when a subscription is created/renewed for a restaurant.
     * Skips items already assigned (any source).
     *
     * @return array{added: int, skipped: int}
     */
    public function syncPlanToRestaurant(SubscriptionPlan $plan, Restaurant $restaurant): array
    {
        $planItemIds = $plan->items()->pluck('items.id');
        $added = 0;
        $skipped = 0;

        foreach ($planItemIds as $itemId) {
            $assignment = RestaurantItemAssignment::firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'item_id' => $itemId],
                [
                    'is_available' => true,
                    'source'       => 'plan',
                    'plan_id'      => $plan->id,
                    'assigned_by'  => auth()->id(),
                ]
            );

            $assignment->wasRecentlyCreated ? $added++ : $skipped++;
        }

        return ['added' => $added, 'skipped' => $skipped];
    }

    /**
     * When an item is added to a plan, push it to all restaurants that currently
     * hold an active subscription for that plan.
     *
     * @return int Number of new assignments created
     */
    public function propagateItemToActivePlanRestaurants(Item $item, SubscriptionPlan $plan): int
    {
        $restaurantIds = $this->activePlanRestaurantIds($plan);
        $added = 0;

        foreach ($restaurantIds as $restaurantId) {
            $assignment = RestaurantItemAssignment::firstOrCreate(
                ['restaurant_id' => $restaurantId, 'item_id' => $item->id],
                [
                    'is_available' => true,
                    'source'       => 'plan',
                    'plan_id'      => $plan->id,
                    'assigned_by'  => auth()->id(),
                ]
            );

            if ($assignment->wasRecentlyCreated) {
                $added++;
            }
        }

        return $added;
    }

    /**
     * When an item is removed from a plan, delete plan-sourced assignments only.
     * Manual assignments (source='manual') are preserved.
     *
     * @return int Number of deleted assignments
     */
    public function retractItemFromPlanRestaurants(Item $item, SubscriptionPlan $plan): int
    {
        return RestaurantItemAssignment::where('item_id', $item->id)
            ->where('plan_id', $plan->id)
            ->where('source', 'plan')
            ->delete();
    }

    /**
     * Handle plan upgrade / downgrade for a restaurant.
     *  - Removes items that came exclusively from the old plan and are not in the new plan.
     *  - Adds all items from the new plan.
     *
     * @return array{added: int, removed: int}
     */
    public function handlePlanChange(
        Restaurant $restaurant,
        SubscriptionPlan $newPlan,
        ?SubscriptionPlan $oldPlan = null
    ): array {
        $removed = 0;

        if ($oldPlan && $oldPlan->id !== $newPlan->id) {
            $newPlanItemIds = $newPlan->items()->pluck('items.id')->toArray();

            // Remove old-plan assignments for items NOT in the new plan
            $removed = RestaurantItemAssignment::where('restaurant_id', $restaurant->id)
                ->where('plan_id', $oldPlan->id)
                ->where('source', 'plan')
                ->whereNotIn('item_id', $newPlanItemIds)
                ->delete();
        }

        $result = $this->syncPlanToRestaurant($newPlan, $restaurant);

        return ['added' => $result['added'], 'removed' => $removed];
    }

    /**
     * Force-sync all items of a plan to ALL restaurants that have an active
     * subscription for it. Used for bulk repair / backfill.
     *
     * @return array{restaurants: int, items_added: int}
     */
    public function syncPlanToAllActiveRestaurants(SubscriptionPlan $plan): array
    {
        $restaurantIds = $this->activePlanRestaurantIds($plan);
        $totalAdded = 0;

        foreach ($restaurantIds as $restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) continue;

            $result = $this->syncPlanToRestaurant($plan, $restaurant);
            $totalAdded += $result['added'];
        }

        return ['restaurants' => count($restaurantIds), 'items_added' => $totalAdded];
    }

    // ──────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────

    /** IDs of restaurants with an active (non-expired) subscription to this plan. */
    private function activePlanRestaurantIds(SubscriptionPlan $plan): array
    {
        return DB::table('subscriptions')
            ->where('subscription_plan_id', $plan->id)
            ->where('status', 'active')
            ->where('ends_at', '>=', now()->toDateString())
            ->pluck('restaurant_id')
            ->unique()
            ->values()
            ->all();
    }
}
