<?php

namespace App\Filament\Resources\MandoResource\Pages;

use App\Filament\Resources\MandoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMando extends EditRecord
{
    protected static string $resource = MandoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
