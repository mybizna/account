<?php

namespace Modules\Account\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Account\Models\Rate;
use Modules\Base\Filament\Resources\BaseResource;
use Modules\Account\Filament\Clusters\Settings\Resources\RateResource\Pages;
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ledger_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('params')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordering')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published')
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
