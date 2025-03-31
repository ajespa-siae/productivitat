<?php

namespace App\Filament\Resources\EmpleadoResource\Pages;

use App\Filament\Resources\EmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\EmpleadosImport;
use Maatwebsite\Excel\Facades\Excel;

class ListEmpleados extends ListRecords
{
    protected static string $resource = EmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('excel')
                        ->label('Archivo Excel')
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Excel::import(new EmpleadosImport, $data['excel']);
                }),
        ];
    }
}
