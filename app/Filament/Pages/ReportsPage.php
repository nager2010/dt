<?php

namespace App\Filament\Pages;

use App\Models\IssuingLicense;
use App\Models\Municipality;
use App\Models\Region;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class ReportsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'التقارير';
    protected static ?string $title = 'تقارير الرخص';
    protected static ?int $navigationSort = 3;

    public $reportType = 'licenses';
    public $startDate;
    public $endDate;
    public $municipality;
    public $region;
    public $status = 'all';
    public $period = 'custom'; // daily, weekly, monthly, quarterly, yearly, custom

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Select::make('reportType')
                            ->label('نوع التقرير')
                            ->options([
                                'licenses' => 'تقرير الرخص',
                                'financial' => 'التقرير المالي',
                            ])
                            ->default('licenses')
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->reportType = $state;
                            }),
                            
                        Select::make('period')
                            ->label('الفترة الزمنية')
                            ->options([
                                'daily' => 'يومي',
                                'weekly' => 'أسبوعي',
                                'monthly' => 'شهري',
                                'quarterly' => 'ربع سنوي',
                                'yearly' => 'سنوي',
                                'custom' => 'تاريخ محدد',
                            ])
                            ->default('custom')
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->period = $state;
                                $this->setPeriodDates();
                            })
                            ->visible(fn () => $this->reportType === 'financial'),

                        DatePicker::make('startDate')
                            ->label('من تاريخ')
                            ->required(),
                            
                        DatePicker::make('endDate')
                            ->label('إلى تاريخ')
                            ->required(),

                        Select::make('status')
                            ->label('حالة الرخصة')
                            ->options([
                                'all' => 'الكل',
                                'active' => 'سارية',
                                'expired' => 'منتهية',
                            ])
                            ->default('all')
                            ->visible(fn () => $this->reportType === 'licenses'),

                        Select::make('municipality')
                            ->label('البلدية')
                            ->options(Municipality::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn () => $this->reportType === 'licenses'),

                        Select::make('region')
                            ->label('المنطقة')
                            ->options(Region::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn () => $this->reportType === 'licenses'),
                    ])
            ]);
    }

    protected function setPeriodDates()
    {
        $now = Carbon::now();
        
        switch ($this->period) {
            case 'daily':
                $this->startDate = $now->startOfDay();
                $this->endDate = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $this->startDate = $now->startOfWeek();
                $this->endDate = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $this->startDate = $now->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'quarterly':
                $this->startDate = $now->startOfQuarter();
                $this->endDate = $now->copy()->endOfQuarter();
                break;
            case 'yearly':
                $this->startDate = $now->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
        }
    }

    public function table(Table $table): Table
    {
        if ($this->reportType === 'financial') {
            return $this->financialTable($table);
        }
        
        return $this->licensesTable($table);
    }

    protected function licensesTable(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = IssuingLicense::query()
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->municipality, fn($q) => $q->where('municipality_id', $this->municipality))
                    ->when($this->region, fn($q) => $q->where('region_id', $this->region))
                    ->when($this->status !== 'all', function($q) {
                        if ($this->status === 'expired') {
                            return $q->where('endDate', '<', Carbon::now());
                        }
                        return $q->where('endDate', '>=', Carbon::now());
                    });

                return $query;
            })
            ->columns([
                TextColumn::make('licenseNumber')
                    ->label('رقم الرخصة')
                    ->searchable(),
                TextColumn::make('projectName')
                    ->label('اسم المشروع')
                    ->searchable(),
                TextColumn::make('municipality.name')
                    ->label('البلدية'),
                TextColumn::make('region.name')
                    ->label('المنطقة'),
                TextColumn::make('licenseDate')
                    ->label('تاريخ الإصدار')
                    ->date(),
                TextColumn::make('endDate')
                    ->label('تاريخ الانتهاء')
                    ->date(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'منتهية' => 'danger',
                        'سارية' => 'success',
                        default => 'warning',
                    }),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('print')
                    ->label('طباعة التقرير')
                    ->icon('heroicon-o-printer')
                    ->action(function () {
                        $query = IssuingLicense::query()
                            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                            ->when($this->municipality, fn($q) => $q->where('municipality_id', $this->municipality))
                            ->when($this->region, fn($q) => $q->where('region_id', $this->region))
                            ->when($this->status !== 'all', function($q) {
                                if ($this->status === 'expired') {
                                    return $q->where('endDate', '<', Carbon::now());
                                }
                                return $q->where('endDate', '>=', Carbon::now());
                            });

                        $licenses = $query->get();
                        
                        $config = [
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'default_font_size' => '12',
                            'default_font' => 'dejavu sans',
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 10,
                            'margin_bottom' => 10,
                        ];
                        
                        $pdf = Pdf::loadView('reports.licenses', [
                            'licenses' => $licenses,
                            'startDate' => $this->startDate,
                            'endDate' => $this->endDate,
                            'status' => $this->status,
                            'municipality' => $this->municipality ? Municipality::find($this->municipality)->name : 'الكل',
                            'region' => $this->region ? Region::find($this->region)->name : 'الكل',
                        ], [], $config);
                        
                        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
                        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
                        $pdf->getDomPDF()->set_option('isFontSubsettingEnabled', true);
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'تقرير-الرخص.pdf');
                    })
            ]);
    }

    protected function financialTable(Table $table): Table
    {
        return $table
            ->query(function () {
                return IssuingLicense::query()
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->selectRaw('
                        DATE(created_at) as date,
                        COUNT(*) as total_licenses,
                        COALESCE(SUM(licenseFee), 0) as total_fees
                    ')
                    ->groupBy('date')
                    ->orderBy('date');
            })
            ->recordUrl(null)
            ->columns([
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_licenses')
                    ->label('عدد الرخص')
                    ->sortable(),
                TextColumn::make('total_fees')
                    ->label('إجمالي الرسوم')
                    ->formatStateUsing(fn ($state) => number_format($state, 3) . ' د.ل')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                \Filament\Tables\Actions\Action::make('print')
                    ->label('طباعة التقرير')
                    ->icon('heroicon-o-printer')
                    ->action(function () {
                        $revenues = IssuingLicense::query()
                            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                            ->selectRaw('
                                DATE(created_at) as date,
                                COUNT(*) as total_licenses,
                                COALESCE(SUM(licenseFee), 0) as total_fees
                            ')
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();
                        
                        if ($revenues->isEmpty()) {
                            Notification::make()
                                ->title('لا توجد بيانات')
                                ->body('لا توجد بيانات مالية في الفترة المحددة')
                                ->danger()
                                ->send();
                            
                            return;
                        }

                        $config = [
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'default_font_size' => '12',
                            'default_font' => 'dejavu sans',
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 10,
                            'margin_bottom' => 10,
                        ];
                        
                        $pdf = Pdf::loadView('reports.financial', [
                            'revenues' => $revenues,
                            'startDate' => $this->startDate,
                            'endDate' => $this->endDate,
                            'period' => $this->period,
                        ], [], $config);
                        
                        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
                        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
                        $pdf->getDomPDF()->set_option('isFontSubsettingEnabled', true);
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'التقرير-المالي.pdf');
                    })
            ]);
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->date;
    }

    protected static string $view = 'filament.pages.reports';
}
