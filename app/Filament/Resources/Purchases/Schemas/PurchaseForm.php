<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('document_id')
                    ->required()
                    ->numeric(),
                TextInput::make('transaction_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('downloaded_at'),
            ]);
    }
}
