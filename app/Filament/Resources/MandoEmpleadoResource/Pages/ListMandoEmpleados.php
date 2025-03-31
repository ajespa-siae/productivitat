<?php

namespace App\Filament\Resources\MandoEmpleadoResource\Pages;

use App\Filament\Resources\MandoEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMandoEmpleados extends ListRecords
{
    protected static string $resource = MandoEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
