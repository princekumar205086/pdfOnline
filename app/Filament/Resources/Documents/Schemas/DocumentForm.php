<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('district')
                    ->required(),
                TextInput::make('anchal')
                    ->required(),
                TextInput::make('mauza')
                    ->required(),
                TextInput::make('thana_no')
                    ->required(),
                FileUpload::make('file_path')
                    ->required()
                    ->disk('private'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
