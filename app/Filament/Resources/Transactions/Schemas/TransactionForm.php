<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('gateway')
                    ->required(),
                TextInput::make('gateway_order_id')
                    ->required(),
                TextInput::make('gateway_payment_id'),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('document_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required(),
                Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }
}
