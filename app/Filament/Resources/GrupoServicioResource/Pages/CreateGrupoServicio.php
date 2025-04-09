<?php

namespace App\Filament\Resources\GrupoServicioResource\Pages;

use App\Filament\Resources\GrupoServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGrupoServicio extends CreateRecord
{
    protected static string $resource = GrupoServicioResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Afegir');
    }
}
