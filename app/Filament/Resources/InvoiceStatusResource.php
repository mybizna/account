<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Account\Models\InvoiceStatus;
use Modules\Base\Filament\Resources\BaseResource;

class InvoiceStatusResource extends BaseResource
{
    protected static ?string $model = InvoiceStatus::class;

    protected static ?string $slug = 'account/invoice/status';

    protected static ?string $navigationGroup = 'Account';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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

}
