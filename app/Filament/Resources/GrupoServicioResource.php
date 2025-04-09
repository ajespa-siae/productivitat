<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GrupoServicioResource\Pages;
use App\Models\GrupoServicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GrupoServicioResource extends Resource
{
    protected static ?string $model = GrupoServicio::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getModelLabel(): string
    {
        return __('filament.resources.GrupoServicio');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.GruposServicios');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('codigo_grupo')
                ->relationship('grupo', 'codigo')
                ->required()
                ->preload()
                ->searchable(),
            Forms\Components\TextInput::make('servicio')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_grupo')
                    ->label(__('filament.columns.grupo'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('servicio')
                    ->label(__('filament.columns.servicio'))
                    ->sortable()
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
            'index' => Pages\ListGrupoServicios::route('/'),
            'create' => Pages\CreateGrupoServicio::route('/create'),
            'edit' => Pages\EditGrupoServicio::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }
}
