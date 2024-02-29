<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentView;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Tapp\FilamentSurvey\FilamentSurveyPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugins([
                FilamentSurveyPlugin::make(),
                SpatieLaravelTranslatablePlugin::make(),
            ])
            ->login()
            // ->colors([
            //     'danger' => Color::Rose,
            //     'gray' => Color::Gray,
            //     'info' => Color::Blue,
            //     'primary' => Color::Amber,
            //     'success' => Color::Emerald,
            //     'warning' => Color::Orange,
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
        FilamentView::registerRenderHook('panels::head.end', fn (): string => Blade::render("@vite('resources/css/app.css')"));

        // Stolen from Shavik https://www.answeroverflow.com/m/1137977978677108816
        // Add Event Listener to allow autofocus() to actually work on modals
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn(): string => new HtmlString(<<<HTML
                <script>
                function autoFocus(element) {
                    let input = element.querySelector("[autofocus='autofocus']");
                    if (input) {
                        setTimeout(function() {
                            input.focus();
                        }, 100);
                    }
                }
        
                window.addEventListener("load", function(e) {
                    autoFocus(document);
        
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes) {
                                mutation.addedNodes.forEach(function(node) {
                                    // Check if it's an Element node and has the x-ref attribute 'modalContainer'
                                    if (node.nodeType === 1 && node.matches("[x-ref='modalContainer']")) { 
                                        autoFocus(node);
                                    }
                                });
                            }
                        });
                    });
        
                    observer.observe(document.body, { childList: true, subtree: true });
                });
        
                </script>
                HTML
            )
        );
    }
}
