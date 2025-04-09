<?php

namespace App\Filament\Resources\RolEmpleadoResource\Pages;

use App\Filament\Resources\RolEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRolEmpleados extends ListRecords
{
    protected static string $resource = RolEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Afegir'),
        ];
    }
}
