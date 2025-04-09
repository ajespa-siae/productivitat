<?php

namespace App\Filament\Resources\ResultadoEvaluacionResource\Pages;

use App\Filament\Resources\ResultadoEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResultadoEvaluacion extends EditRecord
{
    protected static string $resource = ResultadoEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
