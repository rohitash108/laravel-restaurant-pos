<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    /**
     * Demo categories and products with images.
     * Run: php artisan db:seed --class=DemoDataSeeder
     * (Or run after migrations: php artisan migrate:fresh --seed with DatabaseSeeder calling this.)
     */
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (! $restaurant) {
            $this->command->warn('No restaurant found. Run DatabaseSeeder first or migrate:fresh --seed.');
            return;
        }

        // Ensure storage directories exist
        Storage::disk('public')->makeDirectory('items');
        Storage::disk('public')->makeDirectory('categories');

        $this->seedPlaceholderImage();

        // Remove old demo categories/items so we don't duplicate (optional: only if you want fresh demo)
        Category::where('restaurant_id', $restaurant->id)->delete();

        $categories = [
            [
                'name' => 'Sea Food',
                'sort_order' => 0,
                'image' => $this->storePlaceholderImage('categories', 'seafood'),
                'items' => [
                    ['name' => 'Grilled Salmon Steak', 'price' => 80.00, 'food_type' => 'non_veg', 'description' => 'Fresh Atlantic salmon with herbs.'],
                    ['name' => 'Garlic Butter Shrimp', 'price' => 25.00, 'food_type' => 'non_veg', 'description' => 'Jumbo shrimp in garlic butter sauce.'],
                    ['name' => 'Lobster Thermidor', 'price' => 56.00, 'food_type' => 'non_veg', 'description' => 'Half lobster with creamy sauce.'],
                ],
            ],
            [
                'name' => 'Pizza',
                'sort_order' => 1,
                'image' => $this->storePlaceholderImage('categories', 'pizza'),
                'items' => [
                    ['name' => 'Cheese Burst Pizza', 'price' => 66.00, 'food_type' => 'veg', 'description' => 'Loaded with mozzarella.'],
                    ['name' => 'Corn Pizza', 'price' => 96.00, 'food_type' => 'veg', 'description' => 'Sweet corn and cheese.'],
                    ['name' => 'Spinach & Corn Pizza', 'price' => 30.00, 'food_type' => 'veg', 'description' => 'Small 6 inches.'],
                ],
            ],
            [
                'name' => 'Tacos',
                'sort_order' => 2,
                'image' => $this->storePlaceholderImage('categories', 'tacos'),
                'items' => [
                    ['name' => 'Chicken Taco', 'price' => 33.00, 'food_type' => 'non_veg', 'description' => 'Crispy chicken taco.'],
                    ['name' => 'Veggie Taco', 'price' => 22.00, 'food_type' => 'veg', 'description' => 'Fresh vegetables and salsa.'],
                ],
            ],
            [
                'name' => 'Salads',
                'sort_order' => 3,
                'image' => $this->storePlaceholderImage('categories', 'salads'),
                'items' => [
                    ['name' => 'Grilled Chicken Salad', 'price' => 49.00, 'food_type' => 'non_veg', 'description' => 'Mixed greens with grilled chicken.'],
                    ['name' => 'Caesar Salad', 'price' => 38.00, 'food_type' => 'veg', 'description' => 'Romaine, parmesan, croutons.'],
                ],
            ],
            [
                'name' => 'Soups',
                'sort_order' => 4,
                'image' => $this->storePlaceholderImage('categories', 'soups'),
                'items' => [
                    ['name' => 'Tomato Basil Soup', 'price' => 33.00, 'food_type' => 'veg', 'description' => 'Creamy tomato and basil.'],
                    ['name' => 'Chicken Noodle Soup', 'price' => 45.00, 'food_type' => 'non_veg', 'description' => 'Hearty chicken and noodles.'],
                    ['name' => 'Shrimp Tom Yum', 'price' => 25.00, 'food_type' => 'non_veg', 'description' => 'Spicy Thai soup.'],
                ],
            ],
            [
                'name' => 'Drinks',
                'sort_order' => 5,
                'image' => $this->storePlaceholderImage('categories', 'drinks'),
                'items' => [
                    ['name' => 'House Lemonade', 'price' => 4.00, 'food_type' => 'veg', 'description' => 'Fresh lemonade.'],
                    ['name' => 'Lemon Mint Juice', 'price' => 96.00, 'food_type' => 'veg', 'description' => 'Refreshing mint and lemon.'],
                    ['name' => 'Iced Tea', 'price' => 5.00, 'food_type' => 'veg', 'description' => 'Fresh brewed iced tea.'],
                ],
            ],
        ];

        foreach ($categories as $index => $catData) {
            $itemsData = $catData['items'];
            unset($catData['items']);

            $category = Category::create([
                'restaurant_id' => $restaurant->id,
                'name' => $catData['name'],
                'image' => $catData['image'],
                'sort_order' => $catData['sort_order'],
                'is_active' => true,
            ]);

            foreach ($itemsData as $i => $itemData) {
                Item::create([
                    'restaurant_id' => $restaurant->id,
                    'category_id' => $category->id,
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'image' => $this->storePlaceholderImage('items', $category->id . '-' . $i),
                    'price' => $itemData['price'],
                    'food_type' => $itemData['food_type'],
                    'is_available' => true,
                    'sort_order' => $i,
                ]);
            }
        }

        $this->command->info('Demo data seeded: 6 categories with products and placeholder images.');
    }

    private function seedPlaceholderImage(): void
    {
        // Minimal 1x1 PNG (transparent) – reuse for any placeholder
        $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $path = 'items/placeholder.png';
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $tinyPng);
        }
    }

    private function storePlaceholderImage(string $folder, string $key): ?string
    {
        $filename = $key . '.png';
        $path = $folder . '/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        // Try to download a real placeholder image (food/category style)
        $urls = [
            'items' => 'https://picsum.photos/seed/' . md5($key) . '/400/300',
            'categories' => 'https://picsum.photos/seed/' . md5($key) . '/200/200',
        ];
        $url = $urls[$folder] ?? $urls['items'];

        try {
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        // Fallback: copy from placeholder.png or create minimal PNG
        $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        Storage::disk('public')->put($path, $tinyPng);
        return $path;
    }
}
