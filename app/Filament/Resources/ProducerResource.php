<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProducerResource\Pages;
use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProducerResource extends Resource
{
    protected static ?string $model = Producer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->unique('producers', 'brand')
                    ->maxLength(50),
                Forms\Components\Checkbox::make('is_valid'),
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
                Infolists\Components\TextEntry::make('brand'),
                Infolists\Components\TextEntry::make('rate'),
                InfoLists\Components\TextEntry::make('is_valid'),
                InfoLists\Components\TextEntry::make('created_at')->dateTime('Y-m-d'),
                RepeatableEntry::make('branches')->schema([
                    InfoLists\Components\TextEntry::make('name'),
                    InfoLists\Components\TextEntry::make('orders')
                        ->state(fn (Branch $branch): int => $branch->orders()->count()),
                    InfoLists\Components\TextEntry::make('is_default'),
                    InfoLists\Components\TextEntry::make('phone'),
                    InfoLists\Components\TextEntry::make('location.lat')->label('latitude'),
                    InfoLists\Components\TextEntry::make('location.long')->label('longitude'),
                ])->grid()->columnSpanFull()->columns(3),
                RepeatableEntry::make('orders')->schema([
                    InfoLists\Components\TextEntry::make('cost'),
                    InfoLists\Components\TextEntry::make('weight'),
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
            'index' => Pages\ListProducers::route('/'),
            'create' => Pages\CreateProducer::route('/create'),
            'view' => Pages\ViewProducer::route('/{record}'),
            'edit' => Pages\EditProducer::route('/{record}/edit'),
        ];
    }
}
