<?php
namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Modules\Account\Models\Gateway;
use Modules\Base\Filament\Resources\BaseResource;

class GatewayResource extends BaseResource
{
    protected static ?string $model = Gateway::class;

    protected static ?string $slug = 'account/gateway';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';

    protected static ?string $navigationLabel = 'Gateway';

    protected static ?int $navigationSort = 1;

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
                Forms\Components\TextInput::make('ledger_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('currency_id')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('module')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('instruction')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('ordering')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('is_default')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('is_hidden')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('is_hide_in_invoice')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('published')
                    ->required()
                    ->numeric()
                    ->default(0),

                Tabs::make('Tabs')->tabs([
                    Tabs\Tab::make('Rates')
                        ->schema([

                            Repeater::make('rates')
                                ->relationship("rates")
                                ->schema([
                                    Forms\Components\TextInput::make('coupon_id')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ])->columns(2),
                    Tabs\Tab::make('Allowedin')
                        ->schema([
                            Repeater::make('allowedin')
                                ->relationship("allowedin")
                                ->schema([
                                    Forms\Components\TextInput::make('coupon_id')
                                        ->required()
                                        ->maxLength(255),
                                ]),

                        ])->columns(2),
                    Tabs\Tab::make('Disallowedin')
                        ->schema([
                            Repeater::make('disallowedin')
                                ->relationship("disallowedin")
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
