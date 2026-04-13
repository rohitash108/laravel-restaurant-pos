<?php

namespace App\Services\Inventory;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\Order;
use App\Models\RecipeIngredient;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryStockService
{
    /**
     * Resolve or create the default branch for a restaurant (used when no multi-branch UI yet).
     */
    public function defaultBranchId(int $restaurantId): int
    {
        $existing = Branch::where('restaurant_id', $restaurantId)->where('is_default', true)->first();
        if ($existing) {
            return (int) $existing->id;
        }

        $any = Branch::where('restaurant_id', $restaurantId)->orderBy('id')->first();
        if ($any) {
            $any->update(['is_default' => true]);

            return (int) $any->id;
        }

        $branch = Branch::create([
            'restaurant_id' => $restaurantId,
            'name' => 'Main',
            'code' => 'MAIN',
            'is_default' => true,
            'is_active' => true,
        ]);

        return (int) $branch->id;
    }

    /**
     * Stock received (purchase / GRN). Creates a batch and audit movement.
     */
    public function recordStockIn(
        int $restaurantId,
        int $branchId,
        int $ingredientId,
        string $quantity,
        ?string $expiryDate,
        ?string $reference,
        ?string $unitCost,
        ?int $userId,
        ?string $notes = null
    ): IngredientBatch {
        return DB::transaction(function () use ($restaurantId, $branchId, $ingredientId, $quantity, $expiryDate, $reference, $unitCost, $userId, $notes) {
            $qty = $this->decimal($quantity);
            if (bccomp($qty, '0', 6) <= 0) {
                throw new \InvalidArgumentException('Quantity must be positive.');
            }

            $batch = IngredientBatch::create([
                'ingredient_id' => $ingredientId,
                'branch_id' => $branchId,
                'quantity_remaining' => $qty,
                'expiry_date' => $expiryDate,
                'received_at' => now()->toDateString(),
                'reference' => $reference,
                'unit_cost' => $unitCost !== null ? $this->decimal($unitCost) : null,
            ]);

            StockMovement::create([
                'restaurant_id' => $restaurantId,
                'branch_id' => $branchId,
                'ingredient_id' => $ingredientId,
                'type' => StockMovement::TYPE_PURCHASE,
                'quantity_change' => $qty,
                'order_id' => null,
                'order_item_id' => null,
                'user_id' => $userId,
                'notes' => $notes,
                'meta' => ['ingredient_batch_id' => $batch->id],
            ]);

            return $batch;
        });
    }

    /**
     * Wastage / spoilage (FIFO from batches).
     */
    public function recordWaste(
        int $restaurantId,
        int $branchId,
        int $ingredientId,
        string $quantity,
        ?int $userId,
        ?string $notes = null
    ): void {
        $qty = $this->decimal($quantity);
        if (bccomp($qty, '0', 6) <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        DB::transaction(function () use ($restaurantId, $branchId, $ingredientId, $qty, $userId, $notes) {
            $this->deductFifo(
                $branchId,
                $ingredientId,
                $qty,
                StockMovement::TYPE_WASTE,
                $restaurantId,
                null,
                null,
                $userId,
                $notes
            );
        });
    }

    /**
     * Manual adjustment (positive or negative) without batch FIFO — use for corrections.
     */
    public function recordAdjustment(
        int $restaurantId,
        int $branchId,
        int $ingredientId,
        string $signedQuantity,
        ?int $userId,
        ?string $notes = null
    ): void {
        $qty = $this->decimal($signedQuantity);
        if (bccomp($qty, '0', 6) === 0) {
            return;
        }

        DB::transaction(function () use ($restaurantId, $branchId, $ingredientId, $qty, $userId, $notes) {
            if (bccomp($qty, '0', 6) > 0) {
                IngredientBatch::create([
                    'ingredient_id' => $ingredientId,
                    'branch_id' => $branchId,
                    'quantity_remaining' => $qty,
                    'expiry_date' => null,
                    'received_at' => now()->toDateString(),
                    'reference' => 'ADJUSTMENT',
                    'unit_cost' => null,
                ]);
            } else {
                $need = bcmul($qty, '-1', 6);
                $this->deductFifo(
                    $branchId,
                    $ingredientId,
                    $need,
                    StockMovement::TYPE_ADJUSTMENT,
                    $restaurantId,
                    null,
                    null,
                    $userId,
                    $notes
                );

                return;
            }

            StockMovement::create([
                'restaurant_id' => $restaurantId,
                'branch_id' => $branchId,
                'ingredient_id' => $ingredientId,
                'type' => StockMovement::TYPE_ADJUSTMENT,
                'quantity_change' => $qty,
                'user_id' => $userId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * When an order is placed: deduct ingredients per recipe × quantity sold.
     */
    public function deductForOrder(Order $order): void
    {
        $order->loadMissing(['items']);
        $restaurantId = (int) $order->restaurant_id;
        $branchId = $this->defaultBranchId($restaurantId);

        DB::transaction(function () use ($order, $restaurantId, $branchId) {
            foreach ($order->items as $orderItem) {
                $recipes = RecipeIngredient::where('item_id', $orderItem->item_id)->get();
                if ($recipes->isEmpty()) {
                    continue;
                }

                foreach ($recipes as $recipe) {
                    $perUnit = $this->decimal((string) $recipe->quantity);
                    $need = bcmul($perUnit, (string) $orderItem->quantity, 6);
                    if (bccomp($need, '0', 6) <= 0) {
                        continue;
                    }

                    $available = $this->availableQuantity($recipe->ingredient_id, $branchId);
                    if (bccomp($available, $need, 6) < 0) {
                        Log::warning('inventory.insufficient_stock', [
                            'order_id' => $order->id,
                            'ingredient_id' => $recipe->ingredient_id,
                            'needed' => $need,
                            'available' => $available,
                        ]);

                        continue;
                    }

                    $this->deductFifo(
                        $branchId,
                        $recipe->ingredient_id,
                        $need,
                        StockMovement::TYPE_SALE,
                        $restaurantId,
                        $order->id,
                        $orderItem->id,
                        auth()->id(),
                        'Order '.$order->order_number
                    );
                }
            }
        });
    }

    public function availableQuantity(int $ingredientId, int $branchId): string
    {
        $sum = IngredientBatch::where('ingredient_id', $ingredientId)
            ->where('branch_id', $branchId)
            ->sum('quantity_remaining');

        return $this->decimal((string) $sum);
    }

    protected function deductFifo(
        int $branchId,
        int $ingredientId,
        string $need,
        string $movementType,
        int $restaurantId,
        ?int $orderId,
        ?int $orderItemId,
        ?int $userId,
        ?string $notes
    ): void {
        $remaining = $this->decimal($need);
        $batches = IngredientBatch::query()
            ->where('ingredient_id', $ingredientId)
            ->where('branch_id', $branchId)
            ->where('quantity_remaining', '>', 0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $totalOut = '0';

        foreach ($batches as $batch) {
            if (bccomp($remaining, '0', 6) <= 0) {
                break;
            }
            $q = $this->decimal((string) $batch->quantity_remaining);
            $take = bccomp($q, $remaining, 6) <= 0 ? $q : $remaining;
            $batch->quantity_remaining = $this->decimal(bcsub($q, $take, 6));
            $batch->save();
            $totalOut = bcadd($totalOut, $take, 6);
            $remaining = bcsub($remaining, $take, 6);
        }

        if (bccomp($remaining, '0', 6) > 0) {
            throw new \RuntimeException('Insufficient batch quantity after FIFO.');
        }

        $neg = bcmul($totalOut, '-1', 6);

        StockMovement::create([
            'restaurant_id' => $restaurantId,
            'branch_id' => $branchId,
            'ingredient_id' => $ingredientId,
            'type' => $movementType,
            'quantity_change' => $neg,
            'order_id' => $orderId,
            'order_item_id' => $orderItemId,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }

    protected function decimal(string $value): string
    {
        return number_format((float) $value, 6, '.', '');
    }

    /**
     * Ingredients below threshold at a branch (for dashboard alerts).
     *
     * @return array<int, array{ingredient: Ingredient, on_hand: string, threshold: string}>
     */
    public function lowStockAlerts(int $restaurantId, int $branchId): array
    {
        $out = [];
        $ingredients = Ingredient::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->get();

        foreach ($ingredients as $ing) {
            $th = $this->decimal((string) $ing->low_stock_threshold);
            if (bccomp($th, '0', 6) <= 0) {
                continue;
            }
            $onHand = $this->availableQuantity((int) $ing->id, $branchId);
            if (bccomp($onHand, $th, 6) < 0) {
                $out[] = [
                    'ingredient' => $ing,
                    'on_hand' => $onHand,
                    'threshold' => $th,
                ];
            }
        }

        return $out;
    }

    /**
     * Batches expiring within N days (when track_expiry is on).
     *
     * @return \Illuminate\Support\Collection<int, IngredientBatch>
     */
    public function expiringSoonBatches(int $restaurantId, int $branchId, int $withinDays = 7)
    {
        $until = now()->addDays($withinDays)->toDateString();

        return IngredientBatch::query()
            ->whereHas('ingredient', fn ($q) => $q->where('restaurant_id', $restaurantId)->where('track_expiry', true))
            ->where('branch_id', $branchId)
            ->where('quantity_remaining', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', $until)
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->with('ingredient')
            ->orderBy('expiry_date')
            ->get();
    }
}
