<?php

namespace App\Filament\Resources\GrupoServicioResource\Pages;

use App\Filament\Resources\GrupoServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrupoServicio extends EditRecord
{
    protected static string $resource = GrupoServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
