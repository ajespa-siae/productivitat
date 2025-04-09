<?php

namespace App\Filament\Resources\RolEmpleadoResource\Pages;

use App\Filament\Resources\RolEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRolEmpleado extends CreateRecord
{
    protected static string $resource = RolEmpleadoResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Afegir');
    }
}
