<?php

namespace App\Filament\Resources\RolResource\Pages;

use App\Filament\Resources\RolResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRol extends CreateRecord
{
    protected static string $resource = RolResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Afegir');
    }
}
