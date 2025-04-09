<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultadoEvaluacionResource\Pages;
use App\Models\ResultadoEvaluacion;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ResultadoEvaluacionResource extends Resource
{
    protected static ?string $model = ResultadoEvaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function getNavigationLabel(): string
    {
        return 'Resultats';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('evaluacion_id')
                    ->label('Avaluació')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->afterStateHydrated(function ($component, $state) {
                        if ($state) {
                            $evaluacion = \App\Models\Evaluacion::with('evaluado')->find($state);
                            if ($evaluacion) {
                                $component->helperText("Avaluat: {$evaluacion->evaluado->nombre}");
                            }
                        }
                    }),
                Forms\Components\Select::make('indicador_id')
                    ->label('Indicador')
                    ->relationship('indicador', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('puntuacion')
                    ->label('Puntuació')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(0.01)
                    ->default(1)
                    ->required(),
                Forms\Components\Textarea::make('comentario')
                    ->label('Comentari')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluacion.evaluado.nombre')
                    ->label('Avaluat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('evaluacion.evaluador.nombre')
                    ->label('Avaluador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('indicador.nombre')
                    ->label('Indicador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('puntuacion')
                    ->label('Puntuació')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    ),
                Tables\Columns\TextColumn::make('comentario')
                    ->label('Comentari')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResultadoEvaluaciones::route('/'),
            'create' => Pages\CreateResultadoEvaluacion::route('/create'),
            'edit' => Pages\EditResultadoEvaluacion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('evaluacion', function ($query) {
                $query->where('periodo_id', static::getDefaultPeriodoId());
            });
    }

    protected static function getDefaultPeriodoId(): int
    {
        return \App\Models\Periodo::where('activo', true)->value('id') ?? 0;
    }
}
