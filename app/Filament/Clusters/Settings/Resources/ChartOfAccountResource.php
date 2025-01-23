<?php

namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Modules\Account\Filament\Clusters\Settings\Settings;
use Modules\Account\Models\ChartOfAccount;
use Modules\Base\Filament\Resources\BaseResource;

class ChartOfAccountResource extends BaseResource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'account/chart_of_account';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Chart Of Account';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }


}
