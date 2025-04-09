<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionResource\Pages;
use App\Models\Evaluacion;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class EvaluacionResource extends Resource
{
    protected static ?string $model = Evaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function getNavigationLabel(): string
    {
        return 'Avaluacions';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('evaluado_id')
                    ->label('Avaluat')
                    ->relationship('evaluado', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('evaluador_id')
                    ->label('Avaluador')
                    ->relationship('evaluador', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('periodo_id')
                    ->label('Període')
                    ->relationship('periodo', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\DatePicker::make('fecha')
                    ->label('Data')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('tipo')
                    ->label('Tipus')
                    ->options([
                        'Registro' => 'Registre',
                        'Automatico' => 'Automàtic',
                        'Encuesta' => 'Enquesta',
                    ])
                    ->required()
                    ->default('Registro'),
                Forms\Components\Toggle::make('finalizada')
                    ->label('Finalitzada')
                    ->default(false),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluado.nombre')
                    ->label('Avaluat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('evaluador.nombre')
                    ->label('Avaluador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periodo.nombre')
                    ->label('Període')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Data')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipus')
                    ->searchable(),
                Tables\Columns\IconColumn::make('finalizada')
                    ->label('Finalitzada')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipus')
                    ->options([
                        'Registro' => 'Registre',
                        'Automatico' => 'Automàtic',
                        'Encuesta' => 'Enquesta',
                    ]),
                Tables\Filters\TernaryFilter::make('finalizada')
                    ->label('Finalitzada'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluaciones::route('/'),
            'create' => Pages\CreateEvaluacion::route('/create'),
            'edit' => Pages\EditEvaluacion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('periodo_id', static::getDefaultPeriodoId());
    }

    protected static function getDefaultPeriodoId(): int
    {
        return \App\Models\Periodo::where('activo', true)->value('id') ?? 0;
    }
}
