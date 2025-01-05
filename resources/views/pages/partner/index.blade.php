@extends('layouts.app')

@section('title', 'Rekanan')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">Rekanan</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data Rekanan</p>
        </div>
        @if (auth()->user()->can('partner_create') ||auth()->user()->can('partner_export') ||auth()->user()->can('partner_import'))
            <div
                class="inline-flex h-max overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
                role="group"
            >
                @can('partner_create')
                    <button
                        type="button"
                        data-modal-target="create-partner"
                        data-modal-toggle="create-partner"
                        class="bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white"
                    >
                        Tambah
                    </button>
                @endcan

                @can('partner_export')
                    <button
                        type="button"
                        class="border-l border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white"
                    >
                        Export
                    </button>
                @endcan

                @can('partner_import')
                    <button
                        type="button"
                        data-modal-target="import-partner"
                        data-modal-toggle="import-partner"
                        class="border-l border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white"
                    >
                        Import
                    </button>
                @endcan
            </div>
        @endif
    </div>

    <div>
        <form class="mb-4 flex max-w-sm items-center" action="{{ route('partner.view') }}" method="get">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative flex-1">
                <input
                    value="{{ request('search') }}"
                    type="text"
                    id="search"
                    name="search"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                    placeholder="Cari data rekanan ..."
                />
                @if (request('search'))
                    <a
                        href="{{ route('partner.view') }}"
                        class="absolute right-0.5 top-0.5 z-10 rounded-md bg-gray-50 p-2 dark:bg-gray-700"
                    >
                        <i class="fa-regular fa-circle-xmark"></i>
                        <span class="sr-only">Clear</span>
                    </a>
                @endif
            </div>
            <button
                type="submit"
                class="ms-3 aspect-square w-10 rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
            >
                <i class="fa-solid fa-magnifying-glass"></i>
                <span class="sr-only">Search</span>
            </button>
        </form>
    </div>

    <div class="relative overflow-x-auto sm:rounded-md">
        <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
            <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                <tr>
                    <th scope="col" class="w-16 px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Nama Rekanan</th>
                    <th scope="col" class="px-6 py-3">No HP/Kontak</th>
                    <th scope="col" class="px-6 py-3">Alamat</th>
                    @if (auth()->user()->can('partner_delete') ||auth()->user()->can('partner_update'))
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($partners as $partner)
                    <tr
                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80"
                    >
                        <td class="w-16 px-6 py-4">{{ str_pad($partner->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="min-w-[8em] px-6 py-4">{{ $partner->name }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ $partner->phone_number }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ $partner->address }}</td>
                        @if (auth()->user()->can('partner_delete') ||auth()->user()->can('partner_update'))
                            <td class="px-6 py-4">
                                <div class="flex gap-4">
                                    @can('partner_update')
                                        <button
                                            class="partner_edit font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                            data-modal-target="edit-partner"
                                            data-modal-toggle="edit-partner"
                                            data-partner="{{ json_encode($partner) }}"
                                        >
                                            <i class="fa-solid fa-pen-to-square text-base"></i>
                                        </button>
                                    @endcan

                                    @can('partner_delete')
                                        <button
                                            class="delete-confirmation font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                            data-id="{{ $partner->id }}"
                                        >
                                            <i class="fa-solid fa-trash-can text-base"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach

                @if ($partners->isEmpty())
                    <tr
                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80"
                    >
                        <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="5">Tidak ada data rekanan</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @can('partner_import')
            <x-modal id="import-partner" title="Import Rekanan">
                <x-slot name="content">
                    <form action="/partner/import" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="relative">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Pilih file excel
                            </label>
                            <input
                                id="excel_file"
                                type="file"
                                name="excel_file"
                                class="w-full rounded-md border border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-300"
                            />
                        </div>
                        <p class="mt-1 text-sm font-medium italic text-gray-500 dark:text-gray-300">
                            *Pastikan nama rekanan tidak ada yang kosong, jika nama rekanan kosong akan dilewati pada
                            baris tersebut
                        </p>
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan

        @can('partner_create')
            <x-modal id="create-partner" title="Tambah Rekanan">
                <x-slot name="content">
                    <form action="/partner" method="post">
                        @csrf
                        <x-input-label id="name" label="Nama Rekanan" name="name" placeholder="Masukkan nama rekanan" />
                        <x-input-label
                            type="number"
                            id="phone_number"
                            label="Nomor Hp"
                            name="phone_number"
                            placeholder="Masukkan nomor hp/kotak"
                        />
                        <x-input-label
                            id="address"
                            label="Alamat Rekanan"
                            name="address"
                            placeholder="Masukkan alamat rekanan"
                        />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan

        @can('partner_update')
            <x-modal id="edit-partner" title="Update Rekanan">
                <x-slot name="content">
                    <form method="post" id="partner_edit_form">
                        @method('patch')
                        @csrf
                        <x-input-label
                            id="update_name"
                            label="Nama Rekanan"
                            name="name"
                            placeholder="Masukkan nama rekanan"
                        />
                        <x-input-label
                            type="number"
                            id="update_phone_number"
                            label="Nomor Hp"
                            name="phone_number"
                            placeholder="Masukkan alamat rekanan"
                        />
                        <x-input-label
                            id="update_address"
                            label="Alamat Rekanan"
                            name="address"
                            placeholder="Masukkan alamat rekanan"
                        />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan

        @can('partner_delete')
            <form method="post" id="partner_form_delete">
                @method('DELETE')
                @csrf
            </form>
        @endcan
    </div>
    <div class="mt-4">
        {{ $partners->links() }}
    </div>
@endsection

@push('scripts')
    @can('partner_delete')
        <script>
            $(function () {
                $('.partner_edit').on('click', function () {
                    const partner = $(this).data('partner');
                    $('#update_name').val(partner.name);
                    $('#update_address').val(partner.address);
                    $('#update_phone_number').val(partner.phone_number);
                    $('#partner_edit_form').attr('action', `/partner/${partner.id}`);
                });
            });
        </script>
    @endcan

    @can('partner_update')
        <script>
            $(function () {
                $('.delete-confirmation').on('click', function () {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus data rekanan tersebut?',
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
                            const form = $('#partner_form_delete');
                            form.attr('action', `/partner/${id}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endcan
@endpush
