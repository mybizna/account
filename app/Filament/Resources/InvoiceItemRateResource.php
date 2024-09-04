<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Account\Filament\Resources\InvoiceItemRateResource\Pages;
use Modules\Account\Models\InvoiceItemRate;

class InvoiceItemRateResource extends Resource
{
    protected static ?string $model = InvoiceItemRate::class;

    protected static ?string $slug = 'account/invoice/item/rate';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rate_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('invoice_item_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('method')
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('params')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('ordering')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('on_total')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('params')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordering')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceItemRates::route('/'),
            'create' => Pages\CreateInvoiceItemRate::route('/create'),
            'edit' => Pages\EditInvoiceItemRate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
