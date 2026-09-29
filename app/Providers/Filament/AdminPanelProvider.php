<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Models\Product;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use FilamentCraft\FilamentCraftPlugin;
use FilamentCraft\Models\Site;
use FilamentCraft\Routing\DynamicTemplateDefinition;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    private const CELADON = [
        50 => 'oklch(0.97 0.012 172)',
        100 => 'oklch(0.94 0.024 172)',
        200 => 'oklch(0.88 0.042 172)',
        300 => 'oklch(0.79 0.06 172)',
        400 => 'oklch(0.67 0.072 172)',
        500 => 'oklch(0.55 0.072 172)',
        600 => 'oklch(0.46 0.066 172)',
        700 => 'oklch(0.4 0.058 172)',
        800 => 'oklch(0.34 0.05 172)',
        900 => 'oklch(0.29 0.04 172)',
        950 => 'oklch(0.2 0.028 172)',
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->navigationGroups(['Shop', 'Website'])
            ->brandName('Kiln Street')
            ->colors([
                'primary' => self::CELADON,
                'gray' => Color::Zinc,
            ])
            ->plugin(
                FilamentCraftPlugin::make()
                    ->singleSite()
                    ->navigationGroup('Website')
                    ->dynamicTemplates([
                        DynamicTemplateDefinition::make('product')
                            ->path('products/{product}')
                            ->templateSlug('product')
                            ->bind('product', fn (Site $site, string $slug): ?Stringable => Product::query()->published()->where('slug', $slug)->exists()
                                ? Str::of($slug)
                                : null),
                    ])
            )
            ->navigationItems([
                NavigationItem::make('View website')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->group('Website')
                    ->sort(99),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
