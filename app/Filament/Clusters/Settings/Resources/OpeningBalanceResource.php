<?php
namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Filament\Clusters\Settings\Settings;
use Modules\Account\Models\OpeningBalance;
use Modules\Base\Filament\Resources\BaseResource;

class OpeningBalanceResource extends BaseResource
{
    protected static ?string $model = OpeningBalance::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'account/opening_balance';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
}
