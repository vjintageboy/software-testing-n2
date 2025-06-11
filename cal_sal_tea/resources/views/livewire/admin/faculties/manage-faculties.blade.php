<div>
    {{-- Sử dụng slot 'header' của layout để hiển thị tiêu đề trang đúng cách --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Khoa') }}
        </h2>
    </x-slot>

    {{-- Đã thay đổi py-12 thành py-8 để giảm khoảng trắng thừa ở phía trên --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    
                    {{-- Session Messages --}}
                    @if (session()->has('message'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                     @if (session()->has('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex justify-end mb-4">
                        <x-button wire:click="create()">
                            {{ __('Tạo Khoa mới') }}
                        </x-button>
                    </div>

                    {{-- Create/Edit Modal --}}
                    <x-dialog-modal wire:model.live="showModal">
                        <x-slot name="title">
                            {{ $isEditMode ? 'Chỉnh sửa Khoa' : 'Tạo Khoa mới' }}
                        </x-slot>
            
                        <x-slot name="content">
                            <div class="mt-4">
                                <x-label for="name" value="{{ __('Tên Khoa') }}" />
                                <x-input id="name" class="block mt-1 w-full" type="text" wire:model.defer="name" />
                                @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                            </div>
                             <div class="mt-4">
                                <x-label for="abbreviation" value="{{ __('Viết tắt') }}" />
                                <x-input id="abbreviation" class="block mt-1 w-full" type="text" wire:model.defer="abbreviation" />
                                @error('abbreviation') <span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                            </div>
                             <div class="mt-4">
                                <x-label for="description" value="{{ __('Mô tả') }}" />
                                <textarea id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="description" rows="4"></textarea>
                                @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                            </div>
                        </x-slot>
            
                        <x-slot name="footer">
                            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                                {{ __('Hủy') }}
                            </x-secondary-button>
            
                            <x-button class="ms-3" wire:click="store()" wire:loading.attr="disabled">
                                {{ __('Lưu') }}
                            </x-button>
                        </x-slot>
                    </x-dialog-modal>

                    {{-- Faculties Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">STT</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Khoa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Viết tắt</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($faculties as $index => $faculty)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $faculties->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $faculty->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $faculty->abbreviation }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $faculty->id }})" class="text-indigo-600 hover:text-indigo-900">Sửa</button>
                                        <button wire:click="delete({{ $faculty->id }})" class="text-red-600 hover:text-red-900 ms-4">Xóa</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Không có dữ liệu.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                     <div class="mt-4">
                        {{ $faculties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
