<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="table" class="space-y-6">
            {{ $this->form }}
        </form>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
