<?php

namespace App\Filament\Resources\EmpleadoResource\Pages;

use App\Filament\Resources\EmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\EmpleadosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ListEmpleados extends ListRecords
{
    protected static string $resource = EmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Afegir'),
            Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalWidth('xl')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Arxiu Excel')
                        ->disk('local')
                        ->directory('tmp')
                        ->preserveFilenames()
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [Nom, Cognoms, NIF]')
                        ->description('(*) A l\'arxiu Excel posarem el nom, cognoms i NIF de cada empleat')
                        ->collapsible(),
                ])
                ->action(function (array $data): void {
                    try {
                        if (empty($data['file'])) {
                            throw new \Exception('No se ha subido ningún archivo');
                        }

                        $filePath = $data['file'];
                        if (is_array($filePath)) {
                            $filePath = $filePath[0];
                        }

                        if (!Storage::disk('local')->exists($filePath)) {
                            throw new \Exception('El archivo no se ha guardado correctamente');
                        }

                        $fullPath = Storage::disk('local')->path($filePath);
                        
                        // Usamos una transacción para asegurar la integridad de los datos
                        DB::beginTransaction();
                        
                        Excel::import(new EmpleadosImport, $fullPath);
                        
                        DB::commit();
                        
                        // Limpiamos el archivo temporal
                        Storage::disk('local')->delete($filePath);
                        
                        Notification::make()
                            ->title('Importación exitosa')
                            ->success()
                            ->duration(5000)
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        
                        if (!empty($data['file'])) {
                            $filePath = is_array($data['file']) ? $data['file'][0] : $data['file'];
                            if (Storage::disk('local')->exists($filePath)) {
                                Storage::disk('local')->delete($filePath);
                            }
                        }
                        
                        Notification::make()
                            ->title('Error al importar')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(5000)
                            ->send();
                    }
                })
                ->modalHeading('Importar Empleados')
                ->modalButton('Importar'),
        ];
    }
}
