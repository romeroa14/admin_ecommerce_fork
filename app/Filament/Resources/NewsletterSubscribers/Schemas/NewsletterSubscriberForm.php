<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('first_name'),
                TextInput::make('last_name'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('subscribed_at'),
                DateTimePicker::make('unsubscribed_at'),
                TextInput::make('source'),
            ]);
    }
}
