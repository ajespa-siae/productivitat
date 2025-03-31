<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MandoEmpleadoResource\Pages;
use App\Models\MandoEmpleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;

class MandoEmpleadoResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = MandoEmpleado::class;
    protected static ?string $modelLabel = 'Asignación de Mando';
    protected static ?string $pluralModelLabel = 'Asignaciones de Mandos';

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
                    ->sortable(),
                Tables\Columns\TextColumn::make('mando.nif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('empleado.nif')
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
            'index' => Pages\ListMandoEmpleados::route('/'),
            'create' => Pages\CreateMandoEmpleado::route('/create'),
            'edit' => Pages\EditMandoEmpleado::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Organización';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
