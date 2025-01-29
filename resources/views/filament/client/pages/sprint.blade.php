<x-filament-panels::page>
    <style>
        .fi-input-wrp-label{
            --tw-text-opacity: 1;
            color: rgba(var(--primary-600),var(--tw-text-opacity))
        }
    </style>
        {{ $this->form }}
        {{ $this->table }}
</x-filament-panels::page>
