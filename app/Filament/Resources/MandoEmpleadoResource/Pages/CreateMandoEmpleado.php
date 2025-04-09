<?php

namespace App\Filament\Resources\MandoEmpleadoResource\Pages;

use App\Filament\Resources\MandoEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasActivePeriod;

class CreateMandoEmpleado extends CreateRecord
{
    use HasActivePeriod;

    protected static string $resource = MandoEmpleadoResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Afegir');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['periodo_id'] = static::getDefaultPeriodoId();
        return $data;
    }
}
