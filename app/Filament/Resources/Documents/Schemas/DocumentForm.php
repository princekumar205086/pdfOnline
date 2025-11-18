<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Document Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('document_type')
                            ->label('Document Type')
                            ->datalist(
                                Document::query()
                                    ->distinct()
                                    ->pluck('document_type')
                                    ->toArray()
                            )
                            ->required(),
                        TextInput::make('district')
                            ->label('District')
                            ->datalist(
                                Document::query()
                                    ->distinct()
                                    ->pluck('district')
                                    ->toArray()
                            )
                            ->required(),
                        TextInput::make('anchal')
                            ->label('Anchal')
                            ->datalist(
                                Document::query()
                                    ->distinct()
                                    ->pluck('anchal')
                                    ->toArray()
                            )
                            ->required(),
                        TextInput::make('mauza')
                            ->label('Mauza')
                            ->datalist(
                                Document::query()
                                    ->distinct()
                                    ->pluck('mauza')
                                    ->toArray()
                            )
                            ->required(),
                        TextInput::make('thana_no')
                            ->label('Thana No')
                            ->datalist(
                                Document::query()
                                    ->distinct()
                                    ->pluck('thana_no')
                                    ->toArray()
                            )
                            ->required(),
                    ]),

                ComponentsSection::make('Primary File')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Upload PDF')
                            ->disk('private')
                            ->directory('documents')
                            ->preserveFilenames()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(512000)
                            ->enableOpen()
                            ->enableDownload()
                            ->visibility('private')
                            ->required(),
                    ]),

                ComponentsSection::make('Pricing & Status')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Toggle::make('is_active')
                            ->required(),
                    ]),

                ComponentsSection::make('Additional PDFs')
                    ->columns(1)
                    ->schema([
                        Repeater::make('files')
                            ->relationship('files')
                            ->columns(3)
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('PDF File')
                                    ->disk('private')
                                    ->directory('documents')
                                    ->preserveFilenames()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(512000)
                                    ->enableOpen()
                                    ->enableDownload()
                                    ->visibility('private')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
