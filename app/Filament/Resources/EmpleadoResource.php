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

    public static function getModelLabel(): string
    {
        return __('filament.resources.Empleados');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.Empleados');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getResourceFormSchema());
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
                ->unique(ignoreRecord: true),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('filament.columns.nombre'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->label(__('filament.columns.apellidos'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('nif')
                    ->label(__('filament.columns.nif'))
                    ->searchable(),
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
            ]);
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
        return __('Organization');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
