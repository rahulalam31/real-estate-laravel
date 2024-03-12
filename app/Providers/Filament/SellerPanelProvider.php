/**
 * SellerPanelProvider configures the Filament panel for sellers.
 *
 * This class is responsible for setting up the panel's appearance, resources, pages, widgets, and middleware specific to the seller's panel within the Filament application.
 */
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('seller')
            ->path('seller')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources/Sellers'), for: 'App\\Filament\\Resources\\Sellers')
            ->discoverPages(in: app_path('Filament/Pages/Sellers'), for: 'App\\Filament\\Pages\\Sellers')
            ->discoverWidgets(in: app_path('Filament/Widgets/Sellers'), for: 'App\\Filament\\Widgets\\Sellers')
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
/**
 * Configures the panel for the SellerPanelProvider.
 *
 * This method sets up the default configuration for the seller's panel, including its ID, path, login requirement, color scheme, resources, pages, widgets, and middleware.
 *
 * @param Panel $panel The panel instance to configure.
 * @return Panel The configured panel instance.
 */
