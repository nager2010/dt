<x-filament-panels::page>
    <div class="space-y-6" dir="rtl">
        <div class="fi-section rounded-xl bg-amber-50 shadow-sm ring-1 ring-amber-300 dark:bg-amber-900 dark:ring-amber-500">
            <div class="fi-section-header flex flex-col gap-3 p-6">
                <h3 class="fi-section-header-heading text-xl font-semibold leading-6 text-amber-600 dark:text-amber-200">
                    فحص الرخص عبر QR
                </h3>
                <p class="fi-section-header-description text-amber-500 dark:text-amber-300">
                    قم بتوجيه الكاميرا نحو رمز QR للرخصة أو أدخل رقم الرخصة يدوياً
                </p>
            </div>

            <div class="fi-section-content p-6 pt-0">
                <!-- قسم قارئ QR -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-amber-50 rounded-lg p-4 border border-amber-300 dark:bg-amber-800 dark:border-amber-600">
                            <div id="reader" class="w-full aspect-square"></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <form wire:submit="searchLicense" class="space-y-4">
                            <div class="space-y-2">
                                <label class="inline-flex text-sm font-medium text-amber-600 dark:text-amber-200">
                                    رقم الرخصة
                                </label>
                                
                                <input 
                                    type="text"
                                    wire:model="licenseNumber"
                                    placeholder="أدخل رقم الرخصة"
                                    class="block w-full rounded-lg border-amber-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-amber-600 dark:bg-amber-800 dark:text-amber-200 sm:text-sm"
                                />

                                @error('licenseNumber')
                                    <p class="text-sm text-danger-600 dark:text-danger-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button type="submit" 
                                class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 rounded-lg fi-btn-color-primary bg-amber-600 text-white hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-400 fi-btn-size-md gap-1.5 px-3 py-2 text-sm">
                                بحث
                            </button>
                        </form>
                    </div>
                </div>

                <!-- قسم تفاصيل الرخصة -->
                @if($license)
                    <div class="mt-8 border-t pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="fi-section rounded-xl bg-amber-50 shadow-sm ring-1 ring-amber-300 dark:bg-amber-900 dark:ring-amber-500">
                                <div class="fi-section-header flex flex-col gap-3 p-6">
                                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-amber-600 dark:text-amber-200">
                                        معلومات الرخصة
                                    </h3>
                                </div>

                                <div class="fi-section-content p-6 pt-0">
                                    <dl class="grid grid-cols-2 gap-4">
                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">رقم الرخصة:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $license->licenseNumber }}</dd>

                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">اسم النشاط:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $license->projectName }}</dd>

                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">نوع الرخصة:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $license->licenseType?->name }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="fi-section rounded-xl bg-amber-50 shadow-sm ring-1 ring-amber-300 dark:bg-amber-900 dark:ring-amber-500">
                                <div class="fi-section-header flex flex-col gap-3 p-6">
                                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-amber-600 dark:text-amber-200">
                                        حالة الرخصة
                                    </h3>
                                </div>

                                <div class="fi-section-content p-6 pt-0">
                                    <dl class="grid grid-cols-2 gap-4">
                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">تاريخ الإصدار:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ $license->startDate ? \Carbon\Carbon::parse($license->startDate)->format('Y/m/d') : '-' }}
                                        </dd>

                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">تاريخ الانتهاء:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ $license->endDate ? \Carbon\Carbon::parse($license->endDate)->format('Y/m/d') : '-' }}
                                        </dd>

                                        <dt class="text-sm font-medium text-amber-600 dark:text-amber-200">الحالة:</dt>
                                        <dd>
                                            @php
                                                $isExpired = $license->endDate && \Carbon\Carbon::parse($license->endDate)->isPast();
                                            @endphp
                                            <span @class([
                                                'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                'bg-success-50 text-success-700 ring-success-600/20' => !$isExpired,
                                                'bg-danger-50 text-danger-700 ring-danger-600/20' => $isExpired,
                                            ])>
                                                {{ $isExpired ? 'منتهية' : 'سارية' }}
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
