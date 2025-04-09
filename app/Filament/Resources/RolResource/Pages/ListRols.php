<?php

namespace App\Filament\Resources\RolResource\Pages;

use App\Filament\Resources\RolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\RolesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ListRols extends ListRecords
{
    protected static string $resource = RolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Afegir'),
            Action::make('importarPeriodo')
                ->label('Importar d\'un altre període')
                ->icon('heroicon-o-clock')
                ->modalWidth('xl')
                ->form([
                    \Filament\Forms\Components\Select::make('periodo')
                        ->label('Període')
                        ->options(function () {
                            // Obtener el periodo activo del modelo Rol actual
                            $periodoActual = \App\Models\Rol::where('id', '>', 0)->value('periodo_id');
                            \Illuminate\Support\Facades\Log::info('Periodo actual: ' . $periodoActual);
                            
                            if (!$periodoActual) {
                                return [];
                            }

                            return DB::table('periodos')
                                ->whereExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('roles')
                                        ->whereColumn('periodos.id', 'roles.periodo_id');
                                })
                                ->where('id', '!=', $periodoActual)
                                ->tap(function ($query) {
                                    \Illuminate\Support\Facades\Log::info('SQL: ' . $query->toSql());
                                    \Illuminate\Support\Facades\Log::info('Bindings: ' . json_encode($query->getBindings()));
                                })
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
                        
                        // Obtenir els rols del període seleccionat
                        $roles = DB::table('roles')
                            ->where('periodo_id', $data['periodo'])
                            ->get();
                            
                        if ($roles->isEmpty()) {
                            throw new \Exception('No hi ha rols per importar en el període seleccionat');
                        }

                        foreach ($roles as $rol) {
                            // Verificar si ya existe un rol con el mismo nombre en el periodo actual
                            $existingRol = DB::table('roles')
                                ->where('periodo_id', session()->get('periodo_id'))
                                ->where('nombre', $rol->nombre)
                                ->first();

                            if (!$existingRol) {
                                DB::table('rols')->insert([
                                    'nombre' => $rol->nombre,
                                    'descripcio' => $rol->descripcio,
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
                ->modalHeading('Importar rols d\'un altre període')
                ->modalButton('Importar'),
            Action::make('importar')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalWidth('xl')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Arxiu Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                    \Filament\Forms\Components\Section::make('Columnes necessàries: [Nom, Codi]')
                        ->description('(*) A l\'arxiu Excel posarem el nom del rol i el seu codi')
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
                        
                        Excel::import(new RolesImport, $fullPath);
                        
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
                ->modalHeading('Importar rols')
                ->modalWidth('md')
                ->modalButton('Importar'),
        ];
    }
}
