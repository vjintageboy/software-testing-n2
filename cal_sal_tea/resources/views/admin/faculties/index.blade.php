<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Khoa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Dòng này sẽ gọi và hiển thị Livewire component của bạn --}}
            @livewire('admin.faculties.manage-faculties')
        </div>
    </div>
</x-app-layout>
