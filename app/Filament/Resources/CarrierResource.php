<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CarrierResource\Pages;
use App\Filament\Resources\CarrierResource\RelationManagers;
use App\Models\V1\Carrier;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CarrierResource extends Resource
{
    protected static ?string $model = Carrier::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('user_id')->searchable(),
                Tables\Columns\TextColumn::make('first_name')->searchable()->sortable(),
                Tables\Columns\CheckboxColumn::make('is_valid'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders'),
                Tables\Columns\TextColumn::make('transportation.name'),
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
                Infolists\Components\TextEntry::make('first_name'),
                Infolists\Components\TextEntry::make('last_name'),
                Infolists\Components\TextEntry::make('rate'),
                InfoLists\Components\TextEntry::make('is_valid'),
                InfoLists\Components\TextEntry::make('created_at')->dateTime('Y-m-d'),
                InfoLists\Components\Section::make('Details')
                    ->description('this is carrier details')
                    ->schema([
                        Infolists\Components\TextEntry::make('details.plate_number')->label(__('plate_number')),
                        Infolists\Components\TextEntry::make('details.brand')->label(__('brand')),
                        Infolists\Components\TextEntry::make('details.model')->label(__('model')),
                        Infolists\Components\TextEntry::make('details.color')->label(__('color')),
                        Infolists\Components\TextEntry::make('details.year')->label(__('year')),
                    ])
                    ->collapsible()->columns(5),
            ])->columns(5);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
            RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarriers::route('/'),
            'create' => Pages\CreateCarrier::route('/create'),
            'view' => Pages\ViewCarrier::route('/{record}'),
            'edit' => Pages\EditCarrier::route('/{record}/edit'),
        ];
    }
}
