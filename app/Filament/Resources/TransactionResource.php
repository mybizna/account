<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Models\Transaction;
use Modules\Base\Filament\Resources\BaseResource;

class TransactionResource extends BaseResource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $slug = 'account/invoice/transaction';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';
    protected static ?string $navigationLabel = 'Transaction';

    protected static ?int $navigationSort = 1;

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


}
