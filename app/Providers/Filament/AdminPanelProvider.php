<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\NavigationResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\UsersResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ShippingTypeResource;
use App\Filament\Resources\StockResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\AdminChatPage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->homeUrl('/admin')
            ->login()
                    ->plugin(
            ThemesPlugin::make()
        )
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                AdminChatPage::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder
            {
                return $builder->groups([
                    NavigationGroup::make('Shop')->items([
                        ...ProductResource::getNavigationItems(),
                        ...OrderResource::getNavigationItems(),
                        ...StockResource::getNavigationItems(),
                        ...ShippingTypeResource::getNavigationItems(),
                    ]),
                    NavigationGroup::make('Content')->items([
                        ...PostResource::getNavigationItems(),
                        ...CategoryResource::getNavigationItems(),
                        ...NavigationResource::getNavigationItems(),
                    ]),

                    NavigationGroup::make('Users & Roles')->items([
                        ...UsersResource::getNavigationItems(),
                        ...AdminChatPage::getNavigationItems(),
                    ]),

                ]);
            })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
