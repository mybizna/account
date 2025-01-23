<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Models\Ledger;
use Modules\Base\Filament\Resources\BaseResource;

class LedgerResource extends BaseResource
{
    protected static ?string $model = Ledger::class;

    protected static ?string $slug = 'account/ledger';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account';
    protected static ?string $navigationLabel = 'Ledger';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('chart_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('code')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('unused')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('is_system')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }



}
