<div class="flex items-center justify-between mb-4">
    <div class="flex gap-4 p-4 bg-gray-100 rounded-lg">
        <div>
            <span class="font-bold ml-2">إجمالي عدد الرخص:</span>
            {{ number_format($total_licenses) }}
        </div>
        <div class="border-r pr-4">
            <span class="font-bold ml-2">إجمالي الرسوم:</span>
            {{ number_format($total_fees, 3) }} د.ل
        </div>
    </div>
</div>
