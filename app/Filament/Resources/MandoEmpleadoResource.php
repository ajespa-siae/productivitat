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

    public static function getModelLabel(): string
    {
        return __('filament.resources.MandoEmpleados');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.MandoEmpleados');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(app(static::class)->getResourceFormSchema());
    }

    protected function getResourceFormSchema(): array
    {
        return [
            Forms\Components\Select::make('mando_id')
                ->required()
                ->preload()
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    return \App\Models\Empleado::query()
                        ->whereIn('nif', function ($query) {
                            $query->select('nif')
                                ->from('mandos')
                                ->where('periodo_id', static::getDefaultPeriodoId());
                        })
                        ->where(function ($query) use ($search) {
                            $query->where('nombre', 'ilike', "%{$search}%")
                                ->orWhere('apellidos', 'ilike', "%{$search}%")
                                ->orWhere('nif', 'ilike', "%{$search}%");
                        })
                        ->get()
                        ->mapWithKeys(function ($empleado) {
                            $mando = \App\Models\Mando::where('nif', $empleado->nif)
                                ->where('periodo_id', static::getDefaultPeriodoId())
                                ->first();
                            return [$mando->id => "{$empleado->nombre} {$empleado->apellidos} ({$empleado->nif})"];
                        })
                        ->toArray();
                }),
            Forms\Components\Select::make('empleado_id')
                ->required()
                ->preload()
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    return \App\Models\Empleado::query()
                        ->where(function ($query) use ($search) {
                            $query->where('nombre', 'ilike', "%{$search}%")
                                ->orWhere('apellidos', 'ilike', "%{$search}%")
                                ->orWhere('nif', 'ilike', "%{$search}%");
                        })
                        ->get()
                        ->mapWithKeys(function ($empleado) {
                            return [$empleado->id => "{$empleado->nombre} {$empleado->apellidos} ({$empleado->nif})"];
                        })
                        ->toArray();
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
                Tables\Columns\TextColumn::make('mando.empleado.nombre')
                    ->label(__('filament.columns.nombre_mando'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('mando.empleado.apellidos')
                    ->label(__('filament.columns.apellidos_mando'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->label(__('filament.columns.nombre_empleado'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('empleado.apellidos')
                    ->label(__('filament.columns.apellidos_empleado'))
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
            'index' => Pages\ListMandoEmpleados::route('/'),
            'create' => Pages\CreateMandoEmpleado::route('/create'),
            'edit' => Pages\EditMandoEmpleado::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Organization');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }
}
