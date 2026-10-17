<?php

namespace App\Filament\Resources;

use App\Models\Report;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('reason'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReportResource\Pages\ListReports::route('/'),
        ];
    }
}
