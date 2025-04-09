<?php

namespace App\Filament\Resources\ResultadoEvaluacionResource\Pages;

use App\Filament\Resources\ResultadoEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResultadoEvaluaciones extends ListRecords
{
    protected static string $resource = ResultadoEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
