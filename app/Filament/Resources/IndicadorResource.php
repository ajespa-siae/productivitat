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
use App\Models\Competencia;
use App\Models\Grupo;
use App\Models\Rol;

class IndicadorResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Indicador::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function getModelLabel(): string
    {
        return __('filament.resources.Indicadores');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.Indicadores');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(app(static::class)->getResourceFormSchema());
    }

    protected function getResourceFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('nombre')
                ->label('Nom')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('competencia_id')
                ->label('Competència')
                ->relationship('competencia', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return Competencia::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('tipo_evaluacion')
                ->label('Tipus avaluació')
                ->options([
                    'Registre' => 'Registre',
                    'Automàtic' => 'Automàtic',
                    'Enquesta' => 'Enquesta',
                ])
                ->required()
                ->default('Registre'),
            Forms\Components\Select::make('tipo_indicador')
                ->label('Tipus indicador')
                ->options([
                    'Objectiu' => 'Objectiu',
                    'Subjectiu' => 'Subjectiu',
                ])
                ->required(),
            Forms\Components\Select::make('periodicidad')
                ->label('Periodicitat')
                ->options([
                    'Cada 6 mesos' => 'Cada 6 mesos',
                    'Continuat' => 'Continuat',
                ])
                ->required(),
            Forms\Components\Select::make('grupo_id')
                ->relationship('grupo', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return Grupo::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('rol_id')
                ->relationship('rol', 'nombre')
                ->required()
                ->preload()
                ->searchable()
                ->options(function () {
                    return Rol::where('periodo_id', static::getDefaultPeriodoId())
                        ->pluck('nombre', 'id');
                }),
            Forms\Components\Select::make('sentido')
                ->options([
                    'Positiu' => 'Positiu',
                    'Negatiu' => 'Negatiu',
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
            ->columns([
                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->label('Grup')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rol.nombre')
                    ->label('Rol')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nom')
                    ->searchable()
                    ->wrap(false)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('competencia.nombre')
                    ->label('Competència')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_evaluacion')
                    ->label('Tipus avaluació')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_indicador')
                    ->label('Tipus indicador')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periodicidad')
                    ->label('Periodicitat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualitzat el')
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
            'list' => Pages\ListIndicadors::route('/list'),
            'create' => Pages\CreateIndicador::route('/create'),
            'edit' => Pages\EditIndicador::route('/{record}/edit'),
        ];
    }
}
