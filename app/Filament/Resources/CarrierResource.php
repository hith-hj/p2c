<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarrierResource\Pages;
use App\Models\V1\Carrier;
use App\Models\V1\Transportation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;


class CarrierResource extends Resource
{
    protected static ?string $model = Carrier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('first_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('rate')->sortable(),
                Tables\Columns\CheckBoxColumn::make('is_valid'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders'),
                Tables\Columns\TextColumn::make('transportation.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_valid')
                    ->options([ 'not validated','validated', ]),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('first_name'),
                Infolists\Components\TextEntry::make('last_name'),
                Infolists\Components\TextEntry::make('rate'),
                InfoLists\Components\TextEntry::make('is_valid'),
                InfoLists\Components\TextEntry::make('created_at')->dateTime('Y-m-d'),

                RepeatableEntry::make('orders')->schema([
                    InfoLists\Components\TextEntry::make('cost'),
                    InfoLists\Components\TextEntry::make('weight'),
                    InfoLists\Components\TextEntry::make('producer.name'),
                    InfoLists\Components\TextEntry::make('branch.name'),
                    InfoLists\Components\TextEntry::make('customer_name'),
                ])->grid()->columnSpanFull()->columns(2),
            ])->columns(4);
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
            'index' => Pages\ListCarriers::route('/'),
            'create' => Pages\CreateCarrier::route('/create'),
            'view' => Pages\ViewCarrier::route('/{record}'),
            'edit' => Pages\EditCarrier::route('/{record}/edit'),
        ];
    }
}
