<?php

    namespace App\Filament\Resources\Documents\Tables;

    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Tables\Columns\IconColumn;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Columns\ToggleColumn;
    use Filament\Tables\Table;

    class DocumentsTable
    {
        public static function configure(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('title')
                        ->searchable(),
                    TextColumn::make('document_type')
                        ->searchable(),
                    TextColumn::make('district')
                        ->searchable(),
                    TextColumn::make('anchal')
                        ->searchable(),
                    TextColumn::make('mauza')
                        ->searchable(),
                    TextColumn::make('thana_no')
                        ->searchable(),
                    TextColumn::make('additional_title')
                        ->label('File Title')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('additional_file_path')
                        ->label('File Name')
                        ->formatStateUsing(fn ($state, $record) => basename($state ?? $record->file_path))
                        ->searchable(),
                    TextColumn::make('price')
                        ->label('Price')
                        ->getStateUsing(fn ($record) => $record->additional_price ?? $record->price)
                        ->money('INR', true)
                        ->sortable(),
                    ToggleColumn::make('is_active'),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                    //
                ])
                ->recordActions([
                    EditAction::make(),
                ])
                ->toolbarActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]);
        }
    }
