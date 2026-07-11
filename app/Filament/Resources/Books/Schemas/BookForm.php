<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('author')
                    ->required(),

                Select::make('categories')
                    ->relationship(
                        name: 'categories',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Select::make('tags')
                    ->relationship(
                        name: 'tags',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),

                FileUpload::make('cover')
                    ->image()
                    ->disk('public')
                    ->directory('covers')
                    ->imageEditor(),

                Toggle::make('is_premium')
                    ->label('Premium')
                    ->default(false),

                Select::make('status')
                    ->options([
                        'processing' => 'Procesando',
                        'ready' => 'Publicado',
                        'failed' => 'Error',
                    ])
                    ->default('processing')
                    ->required(),

                FileUpload::make('pdf_path')
                    ->label('Libro PDF')
                    ->disk('local')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('pdfs')
                    ->required()
            ]);
    }
}