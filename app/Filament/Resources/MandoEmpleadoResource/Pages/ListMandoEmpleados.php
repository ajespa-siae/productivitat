<?php

namespace App\Filament\Resources\MandoEmpleadoResource\Pages;

use App\Filament\Resources\MandoEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\MandosEmpleadosImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class ListMandoEmpleados extends ListRecords
{
    protected static string $resource = MandoEmpleadoResource::class;

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
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [NIF Comandament, NIF Empleat]')
                        ->description('(*) A l\'arxiu Excel posarem el NIF del comandament i el NIF de l\'empleat que té assignat')
                        ->collapsible(),
                ])
                ->action(function (array $data): void {
                    try {
                        if (empty($data['file'])) {
                            throw new \Exception('No s\'ha pujat cap arxiu');
                        }

                        $filePath = $data['file'];
                        if (is_array($filePath)) {
                            $filePath = $filePath[0];
                        }

                        if (!Storage::disk('local')->exists($filePath)) {
                            throw new \Exception('L\'arxiu no s\'ha guardat correctament');
                        }

                        $fullPath = Storage::disk('local')->path($filePath);
                        
                        // Usamos una transacción para asegurar la integridad de los datos
                        DB::beginTransaction();
                        
                        Excel::import(new MandosEmpleadosImport, $fullPath);
                        
                        DB::commit();
                        
                        // Limpiamos el archivo temporal
                        Storage::disk('local')->delete($filePath);
                        
                        Notification::make()
                            ->title('Importació completada')
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
                            ->title('Error d\'importació')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(5000)
                            ->send();
                    }
                })
                ->modalHeading('Importar relacions comandament-empleat')
                ->modalButton('Importar'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['mando.empleado', 'empleado']);
    }
}
