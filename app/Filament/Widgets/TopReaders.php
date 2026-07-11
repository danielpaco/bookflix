<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Widgets\TableWidget;

class TopReaders extends TableWidget
{
    protected static ?string $heading = 'Top lectores';
    protected static ?int $sort = 3;

    public function table(
        Tables\Table $table
    ): Tables\Table {
        return $table
            ->query(

                User::query()
                    ->withSum(
                        'progress',
                        'reading_time_seconds'
                    )
                    ->withCount('progress')
                    ->orderByDesc(
                        'progress_sum_reading_time_seconds'
                    )
            )

            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('Usuario'),

                Tables\Columns\TextColumn::make(
                        'progress_sum_reading_time_seconds'
                    )
                    ->label('Horas')
                    ->formatStateUsing(
                        fn ($state) =>
                        round($state / 3600, 1)
                    ),

                Tables\Columns\TextColumn::make(
                        'progress_count'
                    )
                    ->label('Libros')
            ]);
    }
}
