<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.email')
                    ->label('Usuario')
                    ->searchable(),

                TextColumn::make('plan.name')
                    ->label('Plan'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('provider'),

                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }
}
