<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('book.title')
                    ->label('Libro')
                    ->searchable(),

                TextColumn::make('page_number')
                    ->sortable(),

                TextColumn::make('file_path')
                    ->copyable(),

                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->headerActions([])
            ->recordActions([]);
    }
}
