<?php

namespace Modules\Account\Filament\Resources;

use Modules\Account\Filament\Resources\TransactionResource\Pages;
use Modules\Account\Filament\Resources\TransactionResource\RelationManagers;
use Modules\Account\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('partner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('left_chart_of_account_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('left_ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('right_chart_of_account_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('right_ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('is_processed')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('left_chart_of_account_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('left_ledger_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('right_chart_of_account_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('right_ledger_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_processed')
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
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
