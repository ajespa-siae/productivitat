<?php

namespace App\Filament\Resources\MandoEmpleadoResource\Pages;

use App\Filament\Resources\MandoEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMandoEmpleado extends EditRecord
{
    protected static string $resource = MandoEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
