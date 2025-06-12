<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttrResource\Pages;
use App\Filament\Resources\AttrResource\RelationManagers;
use App\Models\V1\Attr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttrResource extends Resource
{
    protected static ?string $model = Attr::class;

    protected static ?string $navigationIcon = 'heroicon-o-check';

    protected static ?string $navigationGroup = 'Static';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique('attrs','name')
                    ->required(),
                Forms\Components\TextInput::make('extra_cost_percent')
                    ->numeric()
                    ->minValue(0)
                    ->step(1)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('extra_cost_percent')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAttrs::route('/'),
            'create' => Pages\CreateAttr::route('/create'),
            'edit' => Pages\EditAttr::route('/{record}/edit'),
        ];
    }
}
