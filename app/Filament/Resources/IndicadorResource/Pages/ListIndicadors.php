<?php

namespace App\Filament\Resources\IndicadorResource\Pages;

use App\Filament\Resources\IndicadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndicadors extends ListRecords
{
    protected static string $resource = IndicadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
