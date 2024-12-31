@extends('layouts.app')

@section('title', 'Akses Sistem')

@section('content')
    <x-user-control-header />
    <x-button class="mt-6" data-modal-target="create-role-modal" data-modal-toggle="create-role-modal">
        <i class="fa-solid fa-plus"></i>
        Add Access
    </x-button>
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2x:grid-cols-4 mt-4 gap-4">
        @foreach ($roles as $role)
            <div
                class="w-full flex flex-col justify-between p-4 bg-white border border-gray-200 rounded-lg dark:bg-clay dark:border-gray-700">
                <div>
                    <h5 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Nama akses</h5>
                    <div class="flex items-baseline text-gray-900 dark:text-white">
                        <span class="ms-1 text-xl font-normal text-mirage dark:text-white">{{ $role->name }}</span>
                    </div>
                    <p class="mt-4 mb-2 text-gray-500 dark:text-gray-400">Izin akses</p>
                    <ul class="flex flex-wrap gap-2.5 justify-start">
                        @foreach ($role->permissions as $permission)
                            <li class="flex items-center justify-start">
                                <span
                                    class="bg-gray-100 text-gray-600 capitalize text-xs font-medium px-2.5 py-1 rounded dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-500 permission-text">
                                    {{ implode(' ', array_reverse(explode('_', $permission->name))) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <x-button class="mt-6 w-max btn-delete-access" data-access-id="{{ $role->id }}">
                    Hapus
                </x-button>
            </div>
        @endforeach
        <x-modal id="create-role-modal" title="Tambah Akses">
            <x-slot name="content">
                <form action="/control/access" method="post">
                    @csrf
                    <x-input-label id="name" label="Nama Item *" name="name" placeholder="Masukkan nama akses"
                        required />
                    <p class="mt-4 mb-2 text-sm text-mirage dark:text-white font-medium">Pilih izin akses *</p>
                    <ul class="flex flex-wrap gap-2.5">
                        @foreach ($permissions as $permission)
                            <li>
                                <input type="checkbox" id="{{ $permission->name }}" class="hidden peer"
                                    name="{{ $permission->name }}" @checked($permission->name === 'view_dashboard')>
                                <label for="{{ $permission->name }}"
                                    class="select-none inline-flex items-center justify-between w-max px-2.5 py-1 text-sm text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-clay peer-checked:border-red-600 hover:text-gray-600 dark:peer-checked:text-white peer-checked:text-mirage hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                                    <div class="block capitalize">
                                        {{ implode(' ', array_reverse(explode('_', $permission->name))) }}
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
    </div>
    <form method="post" id="delete-access">
        @method('delete')
        @csrf
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.btn-delete-access').on('click', function() {
                const theme = localStorage.getItem('theme');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus akses user terkait?',
                    icon: 'warning',
                    background: theme === 'dark' ? '#212830' : '#fff',
                    color: theme === 'dark' ? '#fff' : '#151B23',
                    showCancelButton: true,
                    confirmButtonColor: '#374557',
                    cancelButtonColor: '#DA2829',
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const id = $(this).data('access-id');
                        const form = $('#delete-access');
                        form.attr('action', `/control/access/${id}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            })
        })
    </script>
@endpush
