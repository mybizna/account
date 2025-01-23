<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Models\Journal;
use Modules\Base\Filament\Resources\BaseResource;

class JournalResource extends BaseResource
{
    protected static ?string $model = Journal::class;

    protected static ?string $slug = 'account/journal';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';
    protected static ?string $navigationLabel = 'Journal';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('grouping_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('partner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('invoice_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('debit')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('credit')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('params')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }


}
