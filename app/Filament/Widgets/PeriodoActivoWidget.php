<?php

namespace App\Filament\Widgets;

use App\Models\Periodo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PeriodoActivoWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return [
                Stat::make('Període Actiu', 'No hi ha període actiu')
                    ->description('Cal activar un període per començar')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $fechaInicio = $periodoActivo->fecha_inicio?->format('d/m/Y') ?? 'No definida';
        $fechaFin = $periodoActivo->fecha_fin?->format('d/m/Y') ?? 'No definida';

        return [
            Stat::make('Període Actiu', $periodoActivo->nombre)
                ->description("$fechaInicio - $fechaFin")
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),
        ];
    }
}
