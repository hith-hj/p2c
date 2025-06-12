<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\V1\Review;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('reviewed')
                    ->default(fn (Review $record): string => class_basename($record->belongTo_type))
                    ->url(function (Review $record): string {
                        $resource = mb_strtolower(class_basename($record->belongTo_type));
                        $route = "filament.admin.resources.{$resource}s.view";

                        return route($route, ['record' => $record->belongTo_id]);
                    })
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('by')
                    ->default(fn (Review $record): string => class_basename($record->reviewer_type))
                    ->url(function (Review $record): string {
                        $resource = mb_strtolower(class_basename($record->reviewer_type));
                        $route = "filament.admin.resources.{$resource}s.view";

                        return route($route, ['record' => $record->reviewer_id]);
                    })
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('rate'),
                Tables\Columns\TextColumn::make('content')->limit(40),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('rate'),
                Infolists\Components\TextEntry::make('content'),
            ])->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
            'view' => Pages\ViewReview::route('/{record}'),
        ];
    }
}
