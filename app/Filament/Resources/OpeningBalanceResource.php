<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Account\Models\OpeningBalance;
use Modules\Base\Filament\Resources\BaseResource;
use Modules\Base\Filament\Resources\Pages;

class OpeningBalanceResource extends BaseResource
{
    protected static ?string $model = OpeningBalance::class;

    protected static ?string $slug = 'account/opening_balance';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';
    protected static ?string $navigationLabel = 'Opening Balance';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('financial_year_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('chart_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('type')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('debit')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('credit')
                    ->required()
                    ->numeric()
                    ->default(0.00),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('financial_year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chart_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ledger_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('debit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
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

    public static function getPages(): array
    {

        Pages\ListBase::setResource(static::class);

        return [
            'index' => Pages\ListBase::route('/'),
            'create' => Pages\CreateBase::route('/create'),
            'edit' => Pages\EditBase::route('/{record}/edit'),
        ];
    }
}
