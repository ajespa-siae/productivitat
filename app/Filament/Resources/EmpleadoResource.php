<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadoResource\Pages;
use App\Models\Empleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;

class EmpleadoResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Empleado::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $pluralModelLabel = 'Empleados';

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    protected static function getResourceFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('apellidos')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('nif')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule) {
                    return $rule->where('periodo_id', static::getDefaultPeriodoId());
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
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rol.nombre')
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
            'index' => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleado::route('/create'),
            'edit' => Pages\EditEmpleado::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Organización';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user';
    }
}
