<?php

namespace App\Filament\Resources;

use App\Models\Municipality;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\MunicipalityResource\Pages;

class MunicipalityResource extends Resource
{
    protected static ?string $model = Municipality::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'البلدية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // حقل إدخال لاسم البلدية
                Forms\Components\TextInput::make('name')
                    ->label('اسم البلدية')
                    ->required()
                    ->maxLength(255),

                // قسم المحلات
                Forms\Components\Repeater::make('regions')
                    ->relationship('regions')
                    ->label('المحلات')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المحلة')
                            ->required(),
                    ])
                    ->label('المحلات')
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // عرض اسم البلدية
                Tables\Columns\TextColumn::make('name')
                    ->label('البلدية')
                    ->searchable(),

                // عرض أسماء المحلات المرتبطة
                Tables\Columns\TextColumn::make('regions.name')
                    ->label('المحلات')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->regions->pluck('name')->join(', ');
                    }),

                // التاريخ
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMunicipalities::route('/'),
            'create' => Pages\CreateMunicipality::route('/create'),
            'edit' => Pages\EditMunicipality::route('/{record}/edit'),
        ];
    }
}
