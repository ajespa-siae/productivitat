<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MandoResource\Pages;
use App\Models\Mando;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;

class MandoResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Mando::class;
    protected static ?string $modelLabel = 'Mando';
    protected static ?string $pluralModelLabel = 'Mandos';

    public static function getNavigationGroup(): ?string
    {
        return 'Organización';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-circle';
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::getFormSchema());
    }

    protected static function getFormSchema(): array
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
                ->required(),
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
            'index' => Pages\ListMandos::route('/'),
            'create' => Pages\CreateMando::route('/create'),
            'edit' => Pages\EditMando::route('/{record}/edit'),
        ];
    }
}
