<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Item;
use App\Models\RecipeIngredient;
use App\Models\StockMovement;
use App\Services\Inventory\InventoryStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    use ResolvesRestaurant;

    public function __construct(
        protected InventoryStockService $stockService
    ) {}

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $branchId = $this->stockService->defaultBranchId($restaurantId);
        $branch = Branch::find($branchId);

        $ingredients = Ingredient::where('restaurant_id', $restaurantId)
            ->orderBy('name')
            ->get();

        $onHand = [];
        foreach ($ingredients as $ing) {
            $onHand[$ing->id] = $this->stockService->availableQuantity((int) $ing->id, $branchId);
        }

        $lowStock = $this->stockService->lowStockAlerts($restaurantId, $branchId);
        $expiring = $this->stockService->expiringSoonBatches($restaurantId, $branchId, 7);

        $recentMovements = StockMovement::where('restaurant_id', $restaurantId)
            ->with(['ingredient', 'branch'])
            ->latest()
            ->limit(25)
            ->get();

        return view('inventory.index', compact(
            'branch',
            'ingredients',
            'onHand',
            'lowStock',
            'expiring',
            'recentMovements'
        ));
    }

    public function ingredientsIndex()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $ingredients = Ingredient::where('restaurant_id', $restaurantId)->orderBy('name')->paginate(20);

        return view('inventory.ingredients-index', compact('ingredients'));
    }

    public function ingredientsCreate()
    {
        return view('inventory.ingredients-form', ['ingredient' => null]);
    }

    public function ingredientsStore(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:64',
            'unit' => 'required|string|max:32',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
            'track_expiry' => 'boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['restaurant_id'] = $restaurantId;
        $data['is_active'] = true;
        $data['track_expiry'] = $request->boolean('track_expiry');
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 0;

        Ingredient::create($data);

        return redirect()->route('inventory.ingredients.index')->with('success', 'Ingredient created.');
    }

    public function ingredientsEdit(Ingredient $ingredient)
    {
        $this->authorizeIngredient($ingredient);

        return view('inventory.ingredients-form', compact('ingredient'));
    }

    public function ingredientsUpdate(Request $request, Ingredient $ingredient)
    {
        $this->authorizeIngredient($ingredient);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:64',
            'unit' => 'required|string|max:32',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
            'track_expiry' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['track_expiry'] = $request->boolean('track_expiry');
        $data['is_active'] = $request->boolean('is_active', true);

        $ingredient->update($data);

        return redirect()->route('inventory.ingredients.index')->with('success', 'Ingredient updated.');
    }

    public function stockInForm()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $branchId = $this->stockService->defaultBranchId($restaurantId);
        $ingredients = Ingredient::where('restaurant_id', $restaurantId)->where('is_active', true)->orderBy('name')->get();

        return view('inventory.stock-in', compact('ingredients', 'branchId'));
    }

    public function stockInStore(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $validated = $request->validate([
            'ingredient_id' => ['required', Rule::exists('ingredients', 'id')->where('restaurant_id', $restaurantId)],
            'quantity' => 'required|numeric|min:0.000001',
            'expiry_date' => 'nullable|date',
            'reference' => 'nullable|string|max:128',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $branchId = $this->stockService->defaultBranchId($restaurantId);

        $this->stockService->recordStockIn(
            $restaurantId,
            $branchId,
            (int) $validated['ingredient_id'],
            (string) $validated['quantity'],
            $validated['expiry_date'] ?? null,
            $validated['reference'] ?? null,
            isset($validated['unit_cost']) ? (string) $validated['unit_cost'] : null,
            auth()->id(),
            $validated['notes'] ?? null
        );

        return redirect()->route('inventory.index')->with('success', 'Stock in recorded.');
    }

    public function wasteForm()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $branchId = $this->stockService->defaultBranchId($restaurantId);
        $ingredients = Ingredient::where('restaurant_id', $restaurantId)->where('is_active', true)->orderBy('name')->get();

        return view('inventory.waste', compact('ingredients', 'branchId'));
    }

    public function wasteStore(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $validated = $request->validate([
            'ingredient_id' => ['required', Rule::exists('ingredients', 'id')->where('restaurant_id', $restaurantId)],
            'quantity' => 'required|numeric|min:0.000001',
            'notes' => 'nullable|string|max:2000',
        ]);

        $branchId = $this->stockService->defaultBranchId($restaurantId);

        $this->stockService->recordWaste(
            $restaurantId,
            $branchId,
            (int) $validated['ingredient_id'],
            (string) $validated['quantity'],
            auth()->id(),
            $validated['notes'] ?? null
        );

        return redirect()->route('inventory.index')->with('success', 'Wastage recorded.');
    }

    public function itemRecipe(Item $item)
    {
        if ((int) $item->restaurant_id !== (int) $this->currentRestaurantId()) {
            abort(403);
        }

        $ingredients = Ingredient::where('restaurant_id', $item->restaurant_id)->where('is_active', true)->orderBy('name')->get();
        $item->load('recipeIngredients.ingredient');

        return view('inventory.item-recipe', compact('item', 'ingredients'));
    }

    public function itemRecipeUpdate(Request $request, Item $item)
    {
        if ((int) $item->restaurant_id !== (int) $this->currentRestaurantId()) {
            abort(403);
        }

        $restaurantId = $this->currentRestaurantId();

        $request->validate([
            'lines' => 'nullable|array',
            'lines.*.ingredient_id' => ['required', Rule::exists('ingredients', 'id')->where('restaurant_id', $restaurantId)],
            'lines.*.quantity' => 'required|numeric|min:0.000001',
        ]);

        $rawLines = $request->input('lines', []);
        $merged = [];
        foreach ($rawLines as $row) {
            $iid = (int) ($row['ingredient_id'] ?? 0);
            if ($iid < 1) {
                continue;
            }
            $qty = (string) $row['quantity'];
            if (! isset($merged[$iid])) {
                $merged[$iid] = '0';
            }
            $merged[$iid] = bcadd($merged[$iid], $qty, 6);
        }

        DB::transaction(function () use ($item, $merged) {
            RecipeIngredient::where('item_id', $item->id)->delete();
            foreach ($merged as $ingredientId => $quantity) {
                if (bccomp($quantity, '0', 6) !== 1) {
                    continue;
                }
                RecipeIngredient::create([
                    'item_id' => $item->id,
                    'ingredient_id' => (int) $ingredientId,
                    'quantity' => $quantity,
                ]);
            }
        });

        return redirect()->route('inventory.item.recipe', $item)->with('success', 'Recipe saved.');
    }

    protected function authorizeIngredient(Ingredient $ingredient): void
    {
        if ((int) $ingredient->restaurant_id !== (int) $this->currentRestaurantId()) {
            abort(403);
        }
    }
}
