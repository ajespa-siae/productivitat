<?php

namespace App\Filament\Resources\RolEmpleadoResource\Pages;

use App\Filament\Resources\RolEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRolEmpleado extends EditRecord
{
    protected static string $resource = RolEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
