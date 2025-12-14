{{-- Confirm Modal Component --}}
@props(['id' => 'confirm-modal', 'title' => '¿Estás seguro?', 'message' => 'Esta acción no se puede deshacer.', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar'])

<div x-data="{ open: false }" x-on:open-modal-{{ $id }}.window="open = true" x-on:close-modal-{{ $id }}.window="open = false">

    {{-- Trigger Slot --}}
    <div @click="open = true">
        {{ $trigger ?? '' }}
    </div>

    {{-- Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;">

        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90" @click.outside="open = false"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4">
                <p class="text-gray-600">{{ $message }}</p>
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button @click="open = false"
                    class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                    {{ $cancelText }}
                </button>
                <div @click="open = false">
                    {{ $confirmButton ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>