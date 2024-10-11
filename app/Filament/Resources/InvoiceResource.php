<?php

namespace Modules\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Account\Models\Invoice;
use Modules\Base\Filament\Resources\BaseResource;
use Modules\Base\Filament\Resources\Pages;

class InvoiceResource extends BaseResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'account/invoice';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Account'; // Group under "Account"
    protected static ?string $navigationLabel = 'Invoice';

    protected static ?int $navigationSort = -1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                // Description Section
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_no')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('partner_id')
                            ->required()
                            ->numeric(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                    ])->columns(3),

                Repeater::make('items')
                    ->relationship("items")

                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ledger_id')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$'),
                        Checkbox::make('has_rates')
                            ->label('Has Rates')
                            ->reactive(), // Make it reactive so it triggers visibility change

                        Repeater::make('rates')
                            ->relationship("rates")
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('rate_id')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('method')
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->required()
                                    ->numeric()
                                    ->default(0.00),
                                Forms\Components\TextInput::make('on_total')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->visible(fn($get) => $get('has_rates') === true)
                            ->label('Invoice Item Rates')
                            ->columnSpanFull()
                            ->columns(6),

                    ])
                    ->label('Invoice Item')
                    ->cloneable()
                    ->columns(5),

                Tabs::make('Tabs')->tabs([
                    Tabs\Tab::make('Payment')
                        ->schema([

                        ])->columns(2),
                    Tabs\Tab::make('Setting')
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'pending' => 'Pending',
                                    'partial' => 'Partial',
                                    'paid' => 'Paid',
                                    'closed' => 'Closed',
                                    'void' => 'Void',
                                ])
                                ->default('draft')
                                ->disabled()
                                ->required(),

                            ToggleButtons::make('is_posted')
                                ->required()
                                ->boolean()
                                ->inline()
                                ->grouped()
                                ->default(0),
                            Forms\Components\TextInput::make('module')
                                ->required()
                                ->maxLength(255)
                                ->readOnly()
                                ->disabled()
                                ->default('Account'),
                            Forms\Components\TextInput::make('model')
                                ->required()
                                ->maxLength(255)
                                ->readOnly()
                                ->disabled()
                                ->default('Invoice'),
                        ])->columns(2),
                ]),

                Textarea::make('description')
                    ->maxLength(255)
                    ->default(null),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('module')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('is_posted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {

        Pages\ListBase::setResource(static::class);

        return [
            'index' => Pages\ListBase::route('/'),
            'create' => Pages\CreateBase::route('/create'),
            'edit' => Pages\EditBase::route('/{record}/edit'),
        ];
    }

}
