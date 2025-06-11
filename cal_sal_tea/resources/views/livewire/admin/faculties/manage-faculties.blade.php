<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    
    <div class="flex justify-between items-center mb-4">
        <div class="w-1/3">
             <x-input type="text" class="w-full" placeholder="Tìm kiếm tên hoặc mã khoa..." wire:model.live.debounce.300ms="search" />
        </div>
        <x-button wire:click="create">
            {{ __('Thêm Khoa mới') }}
        </x-button>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Tên Khoa</th>
                    <th scope="col" class="px-6 py-3">Tên viết tắt</th>
                    <th scope="col" class="px-6 py-3">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faculties as $faculty)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $faculty->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $faculty->name }}</td>
                        <td class="px-6 py-4">{{ $faculty->abbreviation }}</td>
                        <td class="px-6 py-4">
                            <x-button wire:click="edit({{ $faculty->id }})">Sửa</x-button>
                            <x-danger-button wire:click="delete({{ $faculty->id }})" wire:confirm="Bạn có chắc chắn muốn xóa khoa này?">Xóa</x-danger-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">Không tìm thấy khoa nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $faculties->links() }}
    </div>

    <!-- Create/Edit Modal -->
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $isEditMode ? 'Chỉnh sửa Khoa' : 'Tạo Khoa mới' }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label for="name" value="{{ __('Tên Khoa') }}" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="name" />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-label for="abbreviation" value="{{ __('Tên viết tắt') }}" />
                <x-input id="abbreviation" type="text" class="mt-1 block w-full" wire:model="abbreviation" />
                <x-input-error for="abbreviation" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-label for="description" value="{{ __('Mô tả') }}" />
                <textarea id="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="4" wire:model="description"></textarea>
                <x-input-error for="description" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)" wire:loading.attr="disabled">
                {{ __('Hủy') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="save" wire:loading.attr="disabled">
                {{ __('Lưu') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
