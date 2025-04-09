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

    public static function getModelLabel(): string
    {
        return __('filament.resources.Competencia');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.Competencias');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function form(Form $form): Form
    {
        $instance = new static();
        return $form->schema($instance->getFormSchema());
    }

    protected static function getResourceFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('id')
                ->label('ID')
                ->disabled()
                ->visible(fn ($record) => $record !== null),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('filament.columns.nombre'))
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
