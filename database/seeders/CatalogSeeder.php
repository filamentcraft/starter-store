<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CatalogSeeder extends Seeder
{
    private const CATEGORIES = [
        'tea-and-coffee' => ['Tea & coffee', 'Mugs, cups and a teapot, all thrown to be held.', 'dune-mugs'],
        'bowls' => ['Bowls', 'From a small rice bowl to a serving bowl for eight.', 'nesting-bowls'],
        'plates' => ['Plates', 'Dinner and side plates with a lip that stops sauce escaping.', 'dinner-plate-stack'],
        'vases' => ['Vases & bottles', 'For a single stem, or a whole armful from the garden.', 'ring-vase'],
    ];

    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $categories = collect(self::CATEGORIES)->map(fn (array $category, string $slug): Category => Category::query()->create([
            'name' => $category[0],
            'slug' => $slug,
            'description' => $category[1],
            'image' => $this->photo($category[2]),
            'position' => array_search($slug, array_keys(self::CATEGORIES), true),
        ]));

        foreach ($this->products() as $position => $product) {
            Product::query()->create([
                ...$product,
                'category_id' => $categories[$product['category_id']]->getKey(),
                'images' => array_map($this->photo(...), $product['images']),
                'sku' => 'KS-'.str_pad((string) ($position + 1), 3, '0', STR_PAD_LEFT),
                'position' => $position,
            ]);
        }
    }

    private function photo(string $name): string
    {
        $path = "products/{$name}.jpg";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, File::get(resource_path("images/{$name}.jpg")));
        }

        return $path;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function products(): array
    {
        $stoneware = ['Clay' => 'Stoneware', 'Dishwasher' => 'Yes', 'Microwave' => 'Yes'];

        return [
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Tide mug', 'slug' => 'tide-mug', 'price' => 3200, 'stock' => 14,
                'description' => 'Our everyday mug, with a thumb rest on the handle and a wide base that does not tip. The glaze breaks lighter on the throwing lines.',
                'images' => ['tide-mug'], 'variants' => ['Tide blue', 'Oat', 'Charcoal'],
                'details' => ['Capacity' => '350 ml', 'Height' => '10 cm', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Speckle tumbler', 'slug' => 'speckle-tumbler', 'price' => 2400, 'stock' => 22,
                'description' => 'A handleless cup for flat whites, water or a short glass of wine. Unglazed at the foot so you can feel the clay.',
                'images' => ['speckle-tumbler'], 'variants' => [],
                'details' => ['Capacity' => '220 ml', 'Height' => '8.5 cm', ...$stoneware], 'is_new' => true,
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Dune mugs, pair', 'slug' => 'dune-mugs', 'price' => 5600, 'stock' => 6,
                'description' => 'Two mugs in a sand-coloured glaze with a hand-drawn line of peaks. The drawing is different on each one.',
                'images' => ['dune-mugs'], 'variants' => [],
                'details' => ['Capacity' => '300 ml each', 'Set' => '2 mugs', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Ash espresso cups, set of four', 'slug' => 'ash-espresso-cups', 'price' => 4800, 'stock' => 9,
                'description' => 'Small, thick-walled cups that hold the heat of an espresso. Glazed in a pale wood-ash glaze that pools in the texture.',
                'images' => ['ash-cups', 'ash-cups-stack'], 'variants' => [],
                'details' => ['Capacity' => '90 ml each', 'Set' => '4 cups', ...$stoneware], 'is_new' => true,
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Night mug', 'slug' => 'night-mug', 'price' => 3200, 'stock' => 0,
                'description' => 'The Tide mug in a satin black glaze. Back in the next firing.',
                'images' => ['night-mug'], 'variants' => [],
                'details' => ['Capacity' => '350 ml', ...$stoneware],
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Ocean cup', 'slug' => 'ocean-cup', 'price' => 2600, 'compare_at_price' => 3200, 'stock' => 11,
                'description' => 'A small cup with a blue glaze that ran further than planned in the kiln. Seconds price, first-rate cup.',
                'images' => ['ocean-cup'], 'variants' => [],
                'details' => ['Capacity' => '200 ml', ...$stoneware],
            ],
            [
                'category_id' => 'tea-and-coffee', 'name' => 'Brushed teapot', 'slug' => 'brushed-teapot', 'price' => 9800, 'stock' => 4,
                'description' => 'A four-cup teapot with an iron-brown brushed glaze and a lid that stays put when you pour. The spout is cut to stop drips.',
                'images' => ['brushed-teapot'], 'variants' => [],
                'details' => ['Capacity' => '900 ml', 'Serves' => '4 cups', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'bowls', 'name' => 'Everyday bowl', 'slug' => 'everyday-bowl', 'price' => 2800, 'stock' => 30,
                'description' => 'Cereal in the morning, soup at night. Deep enough for both, and it stacks.',
                'images' => ['everyday-bowl'], 'variants' => ['Chalk', 'Oat'],
                'details' => ['Diameter' => '15 cm', 'Depth' => '7 cm', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'bowls', 'name' => 'Nesting bowls, pair', 'slug' => 'nesting-bowls', 'price' => 5200, 'stock' => 8,
                'description' => 'A large and a small bowl in speckled white that sit inside each other on the shelf.',
                'images' => ['nesting-bowls', 'nesting-bowls-stack'], 'variants' => [],
                'details' => ['Diameters' => '18 cm and 14 cm', ...$stoneware],
            ],
            [
                'category_id' => 'bowls', 'name' => 'Pasta bowl', 'slug' => 'pasta-bowl', 'price' => 3400, 'stock' => 16,
                'description' => 'Wide and shallow with a steep wall, so there is room for a proper portion and nothing slides off.',
                'images' => ['pasta-bowl'], 'variants' => [],
                'details' => ['Diameter' => '23 cm', 'Depth' => '5 cm', ...$stoneware], 'is_new' => true,
            ],
            [
                'category_id' => 'bowls', 'name' => 'Tenmoku bowl', 'slug' => 'tenmoku-bowl', 'price' => 4200, 'stock' => 3,
                'description' => 'An iron glaze that fires almost black at the rim and rust-orange where it thins. Each one is unpredictable.',
                'images' => ['tenmoku-bowl'], 'variants' => [],
                'details' => ['Diameter' => '16 cm', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'bowls', 'name' => 'Charcoal rice bowl', 'slug' => 'charcoal-rice-bowl', 'price' => 2200, 'stock' => 25,
                'description' => 'A small footed bowl in a matte charcoal glaze. Sized for rice, noodles or a side of greens.',
                'images' => ['charcoal-bowl'], 'variants' => [],
                'details' => ['Diameter' => '12 cm', ...$stoneware],
            ],
            [
                'category_id' => 'bowls', 'name' => 'Painted serving bowl', 'slug' => 'painted-serving-bowl', 'price' => 6800, 'stock' => 4,
                'description' => 'A red-clay serving bowl, white inside, with grasses painted freehand before the glaze firing.',
                'images' => ['terracotta-bowl'], 'variants' => [],
                'details' => ['Diameter' => '28 cm', 'Clay' => 'Red earthenware', 'Dishwasher' => 'Hand-wash'],
            ],
            [
                'category_id' => 'plates', 'name' => 'Dinner plate', 'slug' => 'dinner-plate', 'price' => 3600, 'stock' => 40,
                'description' => 'A flat plate with a low lip, glazed in chalk white with a speckled rim.',
                'images' => ['dinner-plate', 'dinner-plate-stack'], 'variants' => ['Chalk', 'Oat'],
                'details' => ['Diameter' => '27 cm', ...$stoneware], 'is_featured' => true,
            ],
            [
                'category_id' => 'plates', 'name' => 'Side plate', 'slug' => 'side-plate', 'price' => 2400, 'stock' => 35,
                'description' => 'For toast, cake or small plates. Glazed in a soft grey with an iron rim.',
                'images' => ['side-plate'], 'variants' => [],
                'details' => ['Diameter' => '20 cm', ...$stoneware],
            ],
            [
                'category_id' => 'vases', 'name' => 'Moon bottle', 'slug' => 'moon-bottle', 'price' => 5800, 'stock' => 5,
                'description' => 'A round-bellied bottle vase in speckled white, for a single stem or a couple of dried grasses.',
                'images' => ['moon-bottle'], 'variants' => [],
                'details' => ['Height' => '19 cm', 'Clay' => 'Stoneware', 'Watertight' => 'Yes'], 'is_featured' => true, 'is_new' => true,
            ],
            [
                'category_id' => 'vases', 'name' => 'Ring vase', 'slug' => 'ring-vase', 'price' => 6400, 'stock' => 7,
                'description' => 'Thrown as a closed ring on the wheel, then cut and given a neck. Unglazed outside, glazed within.',
                'images' => ['ring-vase'], 'variants' => [],
                'details' => ['Height' => '24 cm', 'Clay' => 'Stoneware', 'Watertight' => 'Yes'], 'is_new' => true,
            ],
            [
                'category_id' => 'vases', 'name' => 'Handled vase', 'slug' => 'handled-vase', 'price' => 7200, 'stock' => 2,
                'description' => 'A tall vase with two small ear handles, glazed in matte oat.',
                'images' => ['handled-vase'], 'variants' => [],
                'details' => ['Height' => '30 cm', 'Clay' => 'Stoneware', 'Watertight' => 'Yes'],
            ],
            [
                'category_id' => 'vases', 'name' => 'Tall bottle', 'slug' => 'tall-bottle', 'price' => 4600, 'compare_at_price' => 5600, 'stock' => 10,
                'description' => 'A narrow bottle with a blue-grey shino glaze. Last of this glaze, so it is on sale.',
                'images' => ['tall-bottle'], 'variants' => [],
                'details' => ['Height' => '22 cm', 'Clay' => 'Stoneware', 'Watertight' => 'Yes'],
            ],
        ];
    }
}
