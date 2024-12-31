@extends('layouts.app')

@section('title', 'User')

@section('content')
    <x-user-control-header />

    <div class="mt-6">
        <x-button data-modal-target="add-user" data-modal-toggle="add-user">
            <i class="fa-solid fa-user-plus"></i>
            Add User
        </x-button>
    </div>
    <div class="relative overflow-x-auto sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-200">
            <thead class="text-xs text-gray-700 dark:text-gray-200 dark:bg-clay border-b dark:border-b-gray-600">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-[#27313a] uppercase">
                        Nama User
                    </th>
                    <th scope="col" class="px-6 py-3 uppercase">
                        Username
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-[#27313a] uppercase">
                        Akses User
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span data-tooltip-target="tooltip-status" class="hover:cursor-pointer uppercase block w-max">
                            Status Aktif <i class="fa-regular fa-circle-question ml-1"></i>
                        </span>
                        <div id="tooltip-status" role="tooltip"
                            class=" text-xs absolute z-10 invisible inline-block px-3 py-2 font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-600">
                            Jika status non aktif <br>maka user tidak bisa login
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>

                    </th>
                    <th class="px-6 py-4 bg-gray-50 dark:bg-[#27313a] uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-gray-200 dark:border-gray-600 dark:bg-clay">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-[#27313a]">
                            {{ $user->name }}
                            @if (auth()->user()->id == $user->id)
                                <span
                                    class="ms-2 bg-yellow-400 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-500 dark:text-white">
                                    Akun saat ini
                                </span>
                            @endif
                        </th>
                        <td class="px-6 py-4">
                            {{ $user->username }}
                        </td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-[#27313a]">
                            {{ isset($user->roles[0]->name) ? $user->roles[0]->name : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->status == 1 ? 'Aktif' : 'Non Aktif' }}
                        </td>
                        <td class="px-6 py-4 bg-gray-50 dark:bg-[#27313a] flex gap-2">
                            <form action="/control/user/{{ $user->id }}/status" method="POST">
                                @method('PATCH')
                                @csrf
                                <input type="number" name="status" hidden value="{{ $user->status == 1 ? 0 : 1 }}">
                                <button type="submit" {{ auth()->user()->id == $user->id ? 'disabled' : '' }}
                                    class="p-1.5 rounded-md w-8 dark:text-gray-400 dark:hover:text-white hover:dark:bg-gray-600 hover:bg-gray-200/50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                    <i class="fa-solid fa-power-off disbled:text-red-500"></i>
                                </button>
                            </form>
                            <button type="button" {{ auth()->user()->id == $user->id ? 'disabled' : '' }}
                                data-id="{{ $user->id }}"
                                class="delete-user-confirmation p-1.5 rounded-md w-8 hover:dark:bg-gray-600 dark:text-gray-400 dark:hover:text-white divide-amber-50 hover:bg-gray-200/50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            <button type="button" {{ auth()->user()->id == $user->id ? 'disabled' : '' }}
                                data-id="{{ $user->id }}" data-modal-target="add-access" data-modal-toggle="add-access"
                                class="add-access-user p-1.5 rounded-md w-8 hover:dark:bg-gray-600 dark:text-gray-400 dark:hover:text-white divide-amber-50 hover:bg-gray-200/50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="fa-solid fa-user-gear"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-modal id="add-user" title="Tambah User">
            <x-slot name="content">
                <form method="post" id="add-item-form" action="/control/user">
                    @csrf
                    <x-input-label id="name" name="name" label="Nama" placeholder="Nama user" />
                    <x-input-label id="username" name="username" label="Username" placeholder="Username saat login" />
                    <x-input-label id="password" name="password" label="Passowrd" type="password"
                        placeholder="Password user" />
                    <x-input-label id="confirm_password" name="confirm_password" label="Konfirmasi Password" type="password"
                        placeholder="Konfirmasi password" />
                    <div class="w-full">
                        <label for="role" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Akses user
                        </label>
                        <select id="role" name="role"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-600">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-button type="submit">
                            Submit
                        </x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
        <x-modal id="add-access" title="Update Akses">
            <x-slot name="content">
                <form method="post" id="add-access-form">
                    @csrf
                    <div class="w-full">
                        <label for="role" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Akses user
                        </label>
                        <select id="role-update" name="role"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-600">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-button type="submit">
                            Submit
                        </x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>

        <form method="POST" id="form-delete-user">
            @method('DELETE')
            @csrf
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const modalEl = document.querySelector('#add-user')
            const modal = new Modal(modalEl, {}, {
                id: 'add-user',
                override: true
            })

            @if ($errors->any())
                modal.show()
            @endif

            $('.add-access-user').on('click', function() {
                const userId = $(this).data('id')
                $('#add-access-form').attr('action', `/control/user/${userId}/access`);
            });

            $('.delete-user-confirmation').on('click', function() {
                const theme = localStorage.getItem('theme');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus user terkait?',
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
                        const id = $(this).data('id');
                        const form = $('#form-delete-user');
                        form.attr('action', `/control/user/${id}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        })
    </script>
@endpush
