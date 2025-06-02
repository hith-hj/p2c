<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TransportationResource\Pages;
use App\Models\V1\Transportation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransportationResource extends Resource
{
    protected static ?string $model = Transportation::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('initial_cost')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('cancel_cost')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('cost_per_km')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('cost_per_kg')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\Select::make('category')
                    ->options(['car', 'bicycle', 'motorcycle', 'pickup', 'truck'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('capacity')->sortable(),
                Tables\Columns\TextColumn::make('initial_cost'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('carriers_count')->counts('carriers'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(['car', 'bicycle', 'motorcycle', 'pickup', 'truck']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransportations::route('/'),
            'create' => Pages\CreateTransportation::route('/create'),
            'view' => Pages\ViewTransportation::route('/{record}'),
            'edit' => Pages\EditTransportation::route('/{record}/edit'),
        ];
    }
}
