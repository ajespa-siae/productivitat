<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionResource\Pages;
use App\Models\Evaluacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;

class EvaluacionResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Evaluacion::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.Evaluaciones');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.Evaluaciones');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    protected static function getResourceFormSchema(): array
    {
        return [
            Forms\Components\Select::make('mando_id')
                ->relationship('mando', 'nif')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Mando::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nif', 'id');
                }),
            Forms\Components\Select::make('empleado_id')
                ->relationship('empleado', 'nif')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Empleado::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nif', 'id');
                }),
            Forms\Components\Select::make('indicador_id')
                ->relationship('indicador', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Indicador::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\TextInput::make('puntuacion')
                ->required()
                ->numeric()
                ->integer(),
            Forms\Components\Select::make('periodo_id')
                ->relationship('periodo', 'nombre')
                ->required()
                ->hidden(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mando.nombre')
                    ->label(__('filament.columns.mando'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('mando.nif')
                    ->label(__('filament.columns.nif'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->label(__('filament.columns.empleado'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('empleado.nif')
                    ->label(__('filament.columns.nif'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicador.nombre')
                    ->label(__('filament.columns.indicador'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('puntuacion')
                    ->label(__('filament.columns.puntuacion'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->where('periodo_id', static::getDefaultPeriodoId()));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluacions::route('/'),
            'create' => Pages\CreateEvaluacion::route('/create'),
            'edit' => Pages\EditEvaluacion::route('/{record}/edit'),
        ];
    }
}
