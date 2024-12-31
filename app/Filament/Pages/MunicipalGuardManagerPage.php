<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable; // الواجهة المطلوبة
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TextFilter;
use Filament\Tables\Concerns\InteractsWithTable; // الترايت الخاص بالجداول
use App\Models\IssuingLicense;

class MunicipalGuardManagerPage extends Page implements HasTable // إضافة الواجهة هنا
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'إدارة الحرس البلدي';
    protected static ?string $slug = 'municipal-guard-manager';
    protected static string $view = 'filament.pages.municipal-guard-manager-page'; // ربط الصفحة بالعرض الخاص بها


    public function table(Table $table): Table
    {
        return $table
            ->query(
                IssuingLicense::query()
                    ->whereIn('status', ['منتهية', 'سارية']) // البحث في الرخص المنتهية والسارية
            )
            ->columns([
                TextColumn::make('licenseNumber')
                    ->label('رقم الرخصة')
                    ->searchable(),
                TextColumn::make('fullName')
                    ->label('اسم صاحب الرخصة')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (string $state): string => $state === 'منتهية' ? 'منتهية' : 'سارية')
                    ->colors([
                        'danger' => fn ($state): bool => $state === 'منتهية',
                        'success' => fn ($state): bool => $state === 'سارية',
                    ]),
            ])
            ->filters([
//                Filter::make('licenseNumber')
//                    ->label('البحث بـ QR')
//                    ->query(fn ($query, $data) => $query->where('licenseNumber', 'like', '%' . $data . '%'))
//                    ->form([
//                        TextInput::make('licenseNumber')->label('رقم الرخصة'),
//                    ]),
            ]);
    }
}
