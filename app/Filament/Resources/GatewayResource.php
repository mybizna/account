<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
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
                Tables\Columns\TextColumn::make('currency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('module')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instruction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordering')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_default')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_hidden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_hide_in_invoice')
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

}
