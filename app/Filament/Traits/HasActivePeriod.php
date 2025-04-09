<?php

namespace App\Filament\Traits;

use App\Models\Periodo;
use Filament\Forms\Components\Select;

trait HasActivePeriod
{
    protected function getFormSchema(): array
    {
        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo. Por favor, active un periodo antes de continuar.');
        }

        return array_map(function ($field) use ($periodo) {
            if ($field instanceof Select && $field->getName() === 'periodo_id') {
                $field->default($periodo->id)->disabled();
            }
            return $field;
        }, $this->getResourceFormSchema());
    }

    protected function getResourceFormSchema(): array
    {
        return [];
    }

    public static function getDefaultPeriodoId(): int
    {
        $periodo = Periodo::getActivo();
        if (!$periodo) {
            throw new \Exception('No hay ningún periodo activo. Por favor, active un periodo antes de continuar.');
        }
        return $periodo->id;
    }
}
