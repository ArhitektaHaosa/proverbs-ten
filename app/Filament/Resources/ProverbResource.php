<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProverbResource\Pages;
use App\Models\Proverb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProverbResource extends Resource
{
    protected static ?string $model = Proverb::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('language_id')->relationship('language', 'name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Textarea::make('text')->required(),
            Forms\Components\Textarea::make('meaning'),
            Forms\Components\Textarea::make('literal_translation'),
            Forms\Components\Textarea::make('usage_example'),
            Forms\Components\Textarea::make('origin_note'),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved',
                'rejected' => 'Rejected', 'archived' => 'Archived',
            ])->required(),
            Forms\Components\TextInput::make('enrichment_score')->numeric(),
            Forms\Components\TextInput::make('source_url'),
            Forms\Components\Textarea::make('attribution'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('text')->limit(60),
            Tables\Columns\TextColumn::make('language.name'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('enrichment_score'),
            Tables\Columns\TextColumn::make('published_at')->dateTime(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProverbs::route('/'),
            'create' => Pages\CreateProverb::route('/create'),
            'edit' => Pages\EditProverb::route('/{record}/edit'),
        ];
    }
}
