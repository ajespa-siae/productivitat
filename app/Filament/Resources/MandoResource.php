<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MandoResource\Pages;
use App\Models\Mando;
use App\Models\Empleado;
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

    public static function getModelLabel(): string
    {
        return __('filament.resources.Mandos');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.Mandos');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
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
            Forms\Components\Select::make('nif')
                ->label(__('filament.columns.nif'))
                ->required()
                ->searchable()
                ->preload()
                ->getSearchResultsUsing(function (string $search) {
                    return Empleado::query()
                        ->where(function ($query) use ($search) {
                            $query->where('nombre', 'ilike', "%{$search}%")
                                  ->orWhere('apellidos', 'ilike', "%{$search}%")
                                  ->orWhere('nif', 'ilike', "%{$search}%");
                        })
                        ->get()
                        ->mapWithKeys(function ($empleado) {
                            return [$empleado->nif => "{$empleado->nombre} {$empleado->apellidos} ({$empleado->nif})"];
                        })
                        ->toArray();
                })
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
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->label(__('filament.columns.nombre'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('empleado.apellidos')
                    ->label(__('filament.columns.apellidos'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nif')
                    ->label(__('filament.columns.nif'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('grupo.nombre')
                    ->label(__('filament.columns.grupo'))
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

    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
