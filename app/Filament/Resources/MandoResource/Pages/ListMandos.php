<?php

namespace App\Filament\Resources\MandoResource\Pages;

use App\Filament\Resources\MandoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\MandosImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class ListMandos extends ListRecords
{
    protected static string $resource = MandoResource::class;

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
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [NIF, Nom, Cognoms]')
                        ->description('(*) A l\'arxiu Excel posarem el NIF, nom i cognoms de cada comandament')
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
                        
                        Excel::import(new MandosImport, $fullPath);
                        
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
                ->modalHeading('Importar comandaments')
                ->modalButton('Importar'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['empleado', 'grupo']);
    }
}
