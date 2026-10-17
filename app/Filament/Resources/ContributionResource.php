<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContributionResource\Pages;
use App\Models\Contribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')->options([
                'new_proverb' => 'New proverb',
                'translation' => 'Translation',
                'meaning' => 'Meaning',
                'equivalent' => 'Equivalent',
                'usage' => 'Usage',
                'origin' => 'Origin',
                'remix' => 'Remix',
            ]),
            Forms\Components\KeyValue::make('payload'),
            Forms\Components\Select::make('status')->options([
                'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('player.nickname'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContributions::route('/'),
            'edit' => Pages\EditContribution::route('/{record}/edit'),
        ];
    }
}
