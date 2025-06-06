<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\V1\Order;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('producer.brand'),
                Tables\Columns\TextColumn::make('branch.name'),
                Tables\Columns\TextColumn::make('carrier.first_name'),
                Tables\Columns\TextColumn::make('weight'),
                Tables\Columns\TextColumn::make('cost'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                // Tables\Actions\EditAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('id'),
                Infolists\Components\TextEntry::make('serial'),
                Infolists\Components\TextEntry::make('producer.brand'),
                Infolists\Components\TextEntry::make('branch.name'),
                Infolists\Components\TextEntry::make('carrier.first_name'),
                Infolists\Components\TextEntry::make('transportation.name'),
                Infolists\Components\TextEntry::make('weight'),
                Infolists\Components\TextEntry::make('distance'),
                Infolists\Components\TextEntry::make('cost'),
                Infolists\Components\TextEntry::make('delivery_type'),
                Infolists\Components\TextEntry::make('goods_price'),
                Infolists\Components\TextEntry::make('customer.name'),
                Infolists\Components\TextEntry::make('customer.phone'),
                Infolists\Components\TextEntry::make('status')
                    ->formatStateUsing(fn ($state) => OrderStatus::from($state)->name),
                Infolists\Components\TextEntry::make('created_at')->dateTime('Y-m-d'),

            ])->columns(5);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CodesRelationManager::class,
            RelationManagers\AttrsRelationManager::class,
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
