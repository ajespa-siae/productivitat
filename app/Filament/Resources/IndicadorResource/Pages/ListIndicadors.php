<?php

namespace App\Filament\Resources\IndicadorResource\Pages;

use App\Filament\Resources\IndicadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\IndicadoresImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ListIndicadors extends ListRecords
{
    protected static string $resource = IndicadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Afegir'),
            Action::make('importarPeriodo')
                ->label('Importar d\'un altre període')
                ->icon('heroicon-o-clock')
                ->form([
                    \Filament\Forms\Components\Select::make('periodo')
                        ->label('Període')
                        ->options(function () {
                            return DB::table('periodos')
                                ->whereExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('indicadores')
                                        ->whereColumn('periodos.id', 'indicadores.periodo_id');
                                })
                                ->where('id', '!=', session()->get('periodo_id'))
                                ->pluck('nombre', 'id');
                        })
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        if (!session()->has('periodo_id')) {
                            throw new \Exception('No s\'ha seleccionat cap període actiu');
                        }

                        DB::beginTransaction();
                        
                        // Obtenir els indicadors del període seleccionat
                        $indicadores = DB::table('indicadores')
                            ->where('periodo_id', $data['periodo'])
                            ->get();
                            
                        if ($indicadores->isEmpty()) {
                            throw new \Exception('No hi ha indicadors per importar en el període seleccionat');
                        }

                        foreach ($indicadores as $indicador) {
                            // Verificar si ya existe un indicador con la misma combinación en el periodo actual
                            $existingIndicador = DB::table('indicadores')
                                ->where('periodo_id', session()->get('periodo_id'))
                                ->where('competencia_id', $indicador->competencia_id)
                                ->where('grupo_id', $indicador->grupo_id)
                                ->where('rol_id', $indicador->rol_id)
                                ->first();

                            if (!$existingIndicador) {
                                DB::table('indicadores')->insert([
                                    'nombre' => $indicador->nombre,
                                    'competencia_id' => $indicador->competencia_id,
                                    'grupo_id' => $indicador->grupo_id,
                                    'rol_id' => $indicador->rol_id,
                                    'tipo' => $indicador->tipo,
                                    'sentido' => $indicador->sentido,
                                    'valor_minimo' => $indicador->valor_minimo,
                                    'valor_maximo' => $indicador->valor_maximo,
                                    'periodo_id' => session()->get('periodo_id'),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                        
                        DB::commit();
                        
                        Notification::make()
                            ->title('Importació completada')
                            ->success()
                            ->duration(5000)
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        
                        Notification::make()
                            ->title('Error d\'importació')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(5000)
                            ->send();
                    }
                })
                ->modalHeading('Importar indicadors d\'un altre període')
                ->modalWidth('md')
                ->modalButton('Importar'),
            Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Arxiu Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [Codi, Nom, Descripció]')
                        ->description('(*) A l\'arxiu Excel posarem el codi, nom i descripció de cada indicador')
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
                        
                        Excel::import(new IndicadoresImport, $fullPath);
                        
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
                ->modalHeading('Importar indicadors')
                ->modalWidth('md')
                ->modalButton('Importar'),
        ];
    }
}
