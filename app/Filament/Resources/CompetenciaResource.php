<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetenciaResource\Pages;
use App\Models\Competencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasActivePeriod;

class CompetenciaResource extends Resource
{
    use HasActivePeriod;

    protected static ?string $model = Competencia::class;
    protected static ?string $modelLabel = 'Competencia';
    protected static ?string $pluralModelLabel = 'Competencias';

    public static function getNavigationGroup(): ?string
    {
        return 'Evaluación';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-academic-cap';
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
            'index' => Pages\ListCompetencias::route('/'),
            'create' => Pages\CreateCompetencia::route('/create'),
            'edit' => Pages\EditCompetencia::route('/{record}/edit'),
        ];
    }
}
