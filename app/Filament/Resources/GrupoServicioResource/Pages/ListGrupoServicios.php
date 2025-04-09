<?php

namespace App\Filament\Resources\GrupoServicioResource\Pages;

use App\Filament\Resources\GrupoServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Imports\GruposServiciosImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListGrupoServicios extends ListRecords
{
    protected static string $resource = GrupoServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Afegir'),
            Actions\Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->action(function (array $data): void {
                    $file = $data['file'];
                    
                    // Guardar el archivo temporalmente
                    $path = $file->store('temp');
                    
                    try {
                        // Importar el archivo
                        Excel::import(new GruposServiciosImport, Storage::path($path));
                        
                        // Eliminar el archivo temporal
                        Storage::delete($path);
                        
                        // Notificar éxito
                        $this->notify('success', 'Importació completada amb èxit');
                    } catch (\Exception $e) {
                        // Eliminar el archivo temporal
                        Storage::delete($path);
                        
                        // Notificar error
                        $this->notify('danger', $e->getMessage());
                    }
                })
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Arxiu Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [Grup, Servei]')
                        ->description('(*) A l\'arxiu Excel posarem el Codi de grup y a Servei el nom del servei que correspon al grup')
                        ->collapsible(),
                ]),
        ];
    }
}
