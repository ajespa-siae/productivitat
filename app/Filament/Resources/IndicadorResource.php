<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndicadorResource\Pages;
use App\Models\Indicador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;
use Filament\Actions\ImportAction;
use App\Imports\IndicadoresImport;

class IndicadorResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Indicador::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $modelLabel = 'Indicador';
    protected static ?string $pluralModelLabel = 'Indicadores';

    public static function getNavigationGroup(): ?string
    {
        return 'Evaluación';
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    protected static function getResourceFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule) {
                    return $rule->where('periodo_id', static::getDefaultPeriodoId());
                }),
            Forms\Components\TextInput::make('descripcion')
                ->maxLength(255),
            Forms\Components\Select::make('competencia_id')
                ->relationship('competencia', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Competencia::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('grupo_id')
                ->relationship('grupo', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Grupo::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('rol_id')
                ->relationship('rol', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return \App\Models\Rol::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('sentido')
                ->options([
                    'positiu' => 'Positiu',
                    'negatiu' => 'Negatiu',
                ])
                ->required(),
            Forms\Components\TextInput::make('valor_minimo')
                ->required()
                ->numeric()
                ->integer()
                ->minValue(0)
                ->maxValue(10),
            Forms\Components\TextInput::make('valor_maximo')
                ->required()
                ->numeric()
                ->integer()
                ->minValue(0)
                ->maxValue(10),
            Forms\Components\Select::make('periodo_id')
                ->relationship('periodo', 'nombre')
                ->required()
                ->hidden(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(IndicadoresImport::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('competencia.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rol.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sentido')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_minimo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_maximo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListIndicadors::route('/'),
            'create' => Pages\CreateIndicador::route('/create'),
            'edit' => Pages\EditIndicador::route('/{record}/edit'),
        ];
    }
}
