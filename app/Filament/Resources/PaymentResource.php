<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Modules\Account\Models\Payment;
use Modules\Base\Filament\Resources\BaseResource;

class PaymentResource extends BaseResource
{
    protected static ?string $model = Payment::class;

    protected static ?string $slug = 'account/payment';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';
    protected static ?string $navigationLabel = 'Payment';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('partner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('gateway_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('receipt_no')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('code')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('others')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('stage')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('is_posted')
                    ->required()
                    ->numeric()
                    ->default(0),

                Tabs::make('Tabs')->tabs([
                    Tabs\Tab::make('Files')
                        ->schema([

                            Repeater::make('files')
                                ->relationship("files")
                                ->schema([
                                    Forms\Components\TextInput::make('coupon_id')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ])->columns(2),
                ]),
            ]);
    }


}
