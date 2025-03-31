<?php

namespace App\Filament\Resources\MandoResource\Pages;

use App\Filament\Resources\MandoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMandos extends ListRecords
{
    protected static string $resource = MandoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
