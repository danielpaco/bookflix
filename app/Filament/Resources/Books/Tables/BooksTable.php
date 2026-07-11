<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Models\Page;
use App\Jobs\ProcessPdfJob;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('cover')
                    ->disk('public')
                    ->square()
                    ->size(60),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author')
                    ->searchable(),

                TextColumn::make('categories.name')
                    ->badge()
                    ->separator(','),

                TextColumn::make('tags.name')
                    ->badge()
                    ->separator(','),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'processing',
                        'success' => 'ready',
                        'danger' => 'failed',
                    ]),

                IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean(),

                TextColumn::make('pages_count')
                    ->label('Páginas')
                    ->sortable()
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('premium')
                    ->label('Premium')
                    ->icon('heroicon-o-star')
                    ->visible(fn ($record) => ! $record->is_premium)
                    ->action(fn ($record) =>
                        $record->update([
                            'is_premium' => true,
                        ])
                    ),

                Action::make('free')
                    ->label('Gratis')
                    ->icon('heroicon-o-lock-open')
                    ->visible(fn ($record) => $record->is_premium)
                    ->action(fn ($record) =>
                        $record->update([
                            'is_premium' => false,
                        ])
                    ),

                Action::make('reprocess')
                    ->label('Reprocesar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        Page::where('book_id', $record->id)->delete();

                        $record->update([
                            'status' => 'processing',
                            'pages_count' => 0,
                        ]);

                        ProcessPdfJob::dispatch(
                            $record->id,
                            $record->pdf_path
                        );

                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'processing' => 'Procesando',
                        'ready' => 'Publicado',
                        'failed' => 'Error',
                    ]),

                TernaryFilter::make('is_premium'),

                SelectFilter::make('categories')
                    ->relationship('categories', 'name'),

                SelectFilter::make('tags')
                    ->relationship('tags', 'name'),
            ]);
    }
}