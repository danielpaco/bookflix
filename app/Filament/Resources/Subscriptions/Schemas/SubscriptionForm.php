<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->required(),

                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->required(),

                Select::make('status')
                    ->options([
                        'active' => 'Activa',
                        'grace' => 'Gracia',
                        'expired' => 'Expirada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->required(),

                Select::make('provider')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'manual' => 'Manual',
                    ]),

                DateTimePicker::make('starts_at'),

                DateTimePicker::make('expires_at'),

                DateTimePicker::make('grace_ends_at'),

                \Filament\Forms\Components\TextInput::make('provider_subscription_id'),
            ]);
    }
}
