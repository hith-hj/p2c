<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProducerResource\RelationManagers;

use App\Models\V1\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'Branches';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('orders')
                    ->state(fn (Branch $branch): int => $branch->orders()->count()),
                Tables\Columns\TextColumn::make('is_default'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('location.lat')->label('latitude'),
                Tables\Columns\TextColumn::make('location.long')->label('longitude'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
