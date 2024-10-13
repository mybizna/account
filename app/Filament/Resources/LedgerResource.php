<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Account\Filament\Resources\LedgerResource\Pages;
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('chart_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unused')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_system')
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
        return [
            'index' => Pages\Listing::route('/'),
            'create' => Pages\Creating::route('/create'),
            'edit' => Pages\Editing::route('/{record}/edit'),
        ];
    }

}
