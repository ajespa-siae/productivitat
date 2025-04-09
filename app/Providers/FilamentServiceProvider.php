<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Facades\FilamentView;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::sidebar.start',
            fn (): string => "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Buscar el grupo de Administración y contraerlo
                        const groups = document.querySelectorAll('[x-data=\"{ label: \'Administration\' }\"]');
                        groups.forEach(group => {
                            const button = group.querySelector('button');
                            if (button) {
                                // Simular un clic para contraer el grupo
                                button.click();
                            }
                        });
                    });
                </script>
            "
        );
    }
}
