# Working in this project

A Laravel 13 + Filament 5 shop. The storefront pages are built with FilamentCraft; the
catalog, basket and orders are plain Laravel.

- Products, categories and orders are Eloquent models in `app/Models`, each with a custom
  query builder in `app/Models/Builders` holding its scopes (`Product::query()->published()`,
  `Order::query()->open()`). Use those methods instead of raw `where` calls.
- Prices are integers in minor units (pence). Display them with `App\Support\Money::format()`
  and edit them with `App\Filament\Forms\MoneyInput`.
- `App\Storefront\CatalogStorefront` is the bridge FilamentCraft's commerce sections read
  products from and hand orders to. It is bound in `AppServiceProvider`.
- Placing and changing orders goes through `App\Actions\PlaceOrder` and
  `App\Actions\ChangeOrderStatus`, which also move stock. `OrderPlaced` fires the customer
  email and the admin notification.
- Storefront content is seeded from `app/Blueprints/KilnStreetSite.php` and one class per page
  in `app/Blueprints/Pages`. After the first seed it lives in the database and is edited in the
  panel. `php artisan migrate:fresh --seed` rebuilds everything from code.
- Before finishing a change, run `composer test` (Pint, PHPStan level 6, Pest).
