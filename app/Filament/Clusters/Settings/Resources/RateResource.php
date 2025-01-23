<?php

namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Modules\Account\Models\Rate;
use Modules\Base\Filament\Resources\BaseResource;
use Modules\Account\Filament\Clusters\Settings\Settings;

class RateResource extends BaseResource
{
    protected static ?string $model = Rate::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'account/rate';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Rate';

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
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('method')
                    ->required(),
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
                Forms\Components\TextInput::make('published')
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
