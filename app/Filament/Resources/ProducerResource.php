<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProducerResource\Pages;
use App\Filament\Resources\ProducerResource\RelationManagers;
use App\Models\V1\Producer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class ProducerResource extends Resource
{
    protected static ?string $model = Producer::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->unique('producers', 'brand')
                    ->maxLength(50)
                    ->readOnly(),
                Forms\Components\Checkbox::make('is_valid')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('user_id')->searchable(),
                Tables\Columns\TextColumn::make('brand')->searchable(),
                Tables\Columns\CheckboxColumn::make('is_valid'),
                Tables\Columns\TextColumn::make('branches_count')->counts('branches'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_valid')
                    ->options(['not validated', 'validated']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                // Tables\Actions\EditAction::make()->label(''),
                // Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('brand'),
                Infolists\Components\TextEntry::make('rate'),
                Infolists\Components\TextEntry::make('is_valid'),
                Infolists\Components\TextEntry::make('created_at')->dateTime('Y-m-d'),
            ])->columns(4);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BranchesRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducers::route('/'),
            'create' => Pages\CreateProducer::route('/create'),
            'view' => Pages\ViewProducer::route('/{record}'),
            'edit' => Pages\EditProducer::route('/{record}/edit'),
        ];
    }
}
