<?php

namespace App\Filament\Resources;

use App\Enum\Municipality;
use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    // قم بتعطيل الأيقونة والنص الخاص بالتنقل
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = null;

    // أو أضف هذه الدالة لإخفاء المورد من التنقل
    public static function shouldRegisterNavigation(): bool
    {
        return false; // يجعل المورد غير مرئي في التنقل
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('municipality_id')
                    ->label('البلدية')
                    ->options(
                        Municipality::all()->pluck('name', 'id')
                    )
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('municipality.name')
                    ->label('البلدية')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('المحلة')
                    ->searchable(),

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
                //
            ])
            ->actions([
                // قم بإزالة أي إجراء تعديل أو حذف إذا لزم الأمر
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
