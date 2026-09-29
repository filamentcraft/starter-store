<?php

declare(strict_types=1);

namespace App\Storefront;

use App\Actions\PlaceOrder;
use App\Models\Builders\CategoryBuilder;
use App\Models\Builders\ProductBuilder;
use App\Models\Category;
use App\Models\Product;
use FilamentCraft\Commerce\Contracts\Storefront;
use FilamentCraft\Commerce\Data\CatalogQuery;
use FilamentCraft\Commerce\Data\CategoryData;
use FilamentCraft\Commerce\Data\CustomerDetails;
use FilamentCraft\Commerce\Data\OrderResult;
use FilamentCraft\Commerce\Data\ProductData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final readonly class CatalogStorefront implements Storefront
{
    public function __construct(private PlaceOrder $placeOrder) {}

    public function products(CatalogQuery $query): Collection
    {
        return Product::query()
            ->published()
            ->with('category')
            ->when($query->collection === 'featured', fn (ProductBuilder $products): ProductBuilder => $products->featured())
            ->when($query->collection === 'new', fn (ProductBuilder $products): ProductBuilder => $products->newArrivals())
            ->when(filled($query->categorySlug), fn (ProductBuilder $products): ProductBuilder => $products->inCategory((string) $query->categorySlug))
            ->when(filled($query->search), fn (ProductBuilder $products): ProductBuilder => $products->search((string) $query->search))
            ->when($query->minMinor !== null, fn (ProductBuilder $products): ProductBuilder => $products->where('price', '>=', $query->minMinor))
            ->when($query->maxMinor !== null, fn (ProductBuilder $products): ProductBuilder => $products->where('price', '<=', $query->maxMinor))
            ->when(
                $query->sort,
                fn (ProductBuilder $products, string $sort): ProductBuilder => match ($sort) {
                    'price-asc' => $products->orderBy('price'),
                    'price-desc' => $products->orderByDesc('price'),
                    default => $products->ordered(),
                },
            )
            ->when($query->limit, fn (ProductBuilder $products, int $limit): ProductBuilder => $products->limit($limit))
            ->get()
            ->map(fn (Product $product): ProductData => $this->toData($product))
            ->values();
    }

    public function product(string $slug): ?ProductData
    {
        $product = Product::query()->published()->with('category')->where('slug', $slug)->first();

        return $product instanceof Product ? $this->toData($product) : null;
    }

    public function categories(?int $limit = null): Collection
    {
        return Category::query()
            ->ordered()
            ->withCount(['products' => fn (ProductBuilder $products): ProductBuilder => $products->published()])
            ->when($limit, fn (CategoryBuilder $categories, int $limit): CategoryBuilder => $categories->limit($limit))
            ->get()
            ->map(fn (Category $category): CategoryData => new CategoryData(
                slug: $category->slug,
                name: $category->name,
                image: $this->imageUrl($category->image),
                count: (int) $category->products_count,
                url: $this->url('category', ['slug' => $category->slug]),
            ))
            ->values();
    }

    public function category(string $slug): ?CategoryData
    {
        return $this->categories()->firstWhere('slug', $slug);
    }

    /**
     * @param  array<string, string>  $params
     */
    public function url(string $name, array $params = []): string
    {
        return match ($name) {
            'home' => url('/'),
            'shop' => url('shop'),
            'cart' => url('cart'),
            'checkout' => url('checkout'),
            'product' => url('products/'.($params['slug'] ?? '')),
            'category' => url('shop').'?'.http_build_query(['category' => $params['slug'] ?? '']),
            default => '#',
        };
    }

    public function placeOrder(array $lines, CustomerDetails $customer): OrderResult
    {
        $order = $this->placeOrder->handle($lines, $customer);

        if ($order === null) {
            return OrderResult::failed();
        }

        return new OrderResult(
            number: $order->number,
            totalMinor: $order->total,
            itemCount: (int) $order->items->sum('quantity'),
        );
    }

    private function toData(Product $product): ProductData
    {
        return new ProductData(
            slug: $product->slug,
            title: $product->name,
            priceMinor: $product->price,
            compareMinor: $product->compare_at_price ?? 0,
            currencySymbol: (string) config('store.currency.symbol'),
            symbolPosition: (string) config('store.currency.position'),
            images: array_values(array_filter(array_map($this->imageUrl(...), $product->images ?? []))),
            categoryName: $product->category->name ?? '',
            categorySlug: $product->category->slug ?? '',
            description: (string) $product->description,
            variants: array_values($product->variants ?? []),
            attributes: $product->details ?? [],
            inStock: $product->in_stock,
            isNew: $product->is_new,
            isFeatured: $product->is_featured,
            url: $this->url('product', ['slug' => $product->slug]),
            currencyCode: (string) config('store.currency.code'),
        );
    }

    private function imageUrl(?string $path): string
    {
        return blank($path) ? '' : Storage::disk('public')->url($path);
    }
}
