<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                TextInput::make('duration_days')
                    ->numeric()
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
