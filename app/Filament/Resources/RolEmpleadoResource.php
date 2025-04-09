<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolEmpleadoResource\Pages;
use App\Models\RolEmpleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RolEmpleadoResource extends Resource
{
    protected static ?string $model = RolEmpleado::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('filament.resources.RolEmpleado');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.RolEmpleados');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('nif')
                ->relationship(
                    'empleado',
                    'nif',
                    fn ($query) => $query->orderBy('nombre')->orderBy('apellidos')
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} {$record->apellidos} ({$record->nif})")
                ->searchable(['nombre', 'apellidos', 'nif'])
                ->label(__('filament.columns.empleado'))
                ->required()
                ->preload(),
            Forms\Components\Select::make('grupo_id')
                ->relationship('grupo', 'nombre')
                ->required()
                ->preload()
                ->searchable(),
            Forms\Components\Select::make('rol_id')
                ->relationship('rol', 'nombre')
                ->required()
                ->preload()
                ->searchable(),
            Forms\Components\DatePicker::make('fecha_inicio')
                ->required(),
            Forms\Components\DatePicker::make('fecha_fin')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empleado')
                    ->label(__('filament.columns.nombre'))
                    ->getStateUsing(fn ($record) => $record->empleado ? 
                        "{$record->empleado->nombre} {$record->empleado->apellidos}" : '')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereIn('nif', function ($subquery) use ($search) {
                            $subquery->select('nif')
                                ->from('empleados')
                                ->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->label(__('filament.columns.grupo'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('rol.nombre')
                    ->label(__('filament.columns.rol'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label(__('filament.columns.fecha_inicio'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label(__('filament.columns.fecha_fin'))
                    ->date()
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolEmpleados::route('/'),
            'create' => Pages\CreateRolEmpleado::route('/create'),
            'edit' => Pages\EditRolEmpleado::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}   
