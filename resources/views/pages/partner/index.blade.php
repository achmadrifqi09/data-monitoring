@extends('layouts.app')

@section('title', 'Rekanan')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">Rekanan</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data Rekanan</p>
        </div>
        <div class="inline-flex h-max rounded-sm" role="group">
            <button
                type="button"
                data-modal-target="create-partner"
                data-modal-toggle="create-partner"
                class="rounded-s-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Tambah
            </button>
            <button
                type="button"
                class="border-b border-t border-gray-200 bg-white px-4 py-2 text-sm font-medium text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Export
            </button>
            <button
                type="button"
                data-modal-target="import-partner"
                data-modal-toggle="import-partner"
                class="rounded-e-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Import
            </button>
        </div>
    </div>

    <div>
        <form class="mb-4 flex max-w-sm items-center" action="{{ route('partner.view') }}" method="get">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative w-full">
                <input
                    value="{{ request('search') }}"
                    type="text"
                    id="search"
                    name="search"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                    placeholder="Cari rekanan ..."
                />
                @if (request('search'))
                    <a
                        href="{{ route('partner.view') }}"
                        class="absolute right-0.5 top-0.5 z-10 rounded-md bg-gray-50 p-2 dark:bg-gray-700"
                    >
                        <x-solar-close-circle-linear class="h-5 w-5 text-gray-400" />
                        <span class="sr-only">Clear</span>
                    </a>
                @endif
            </div>
            <button
                type="submit"
                class="ms-3 rounded-lg border border-gray-300 bg-white p-[10px] text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
            >
                <x-solar-magnifer-linear class="h-5 w-5" />
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
                    <th scope="col" class="px-6 py-3">Alamat</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($partners as $partner)
                    <tr
                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80"
                    >
                        <td class="w-16 px-6 py-4">{{ str_pad($partner->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="min-w-[8em] px-6 py-4">{{ $partner->name }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ $partner->address }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-4">
                                <button
                                    class="partner_edit font-semibold text-yellow-400"
                                    data-modal-target="edit-partner"
                                    data-modal-toggle="edit-partner"
                                    data-partner="{{ json_encode($partner) }}"
                                >
                                    Edit
                                </button>
                                <button
                                    class="delete-confirmation font-semibold text-red-500"
                                    data-id="{{ $partner->id }}"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($partners->isEmpty())
                    <tr
                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80"
                    >
                        <td class="bg-clay px-6 py-4 text-center" colspan="4">Tidak ada data rekanan</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="mt-4">
            {{ $partners->links() }}
        </div>
        <x-modal id="import-partner" title="Import Rekanan">
            <x-slot name="content">
                <form action="/rekanan/import" method="post" enctype="multipart/form-data">
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
                        *Pastikan nama rekanan tidak ada yang kosong, jika nama rekanan kosong akan dilewati pada baris
                        tersebut
                    </p>
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
        <x-modal id="create-partner" title="Tambah Rekanan">
            <x-slot name="content">
                <form action="/rekanan" method="post">
                    @csrf
                    <x-input-label id="name" label="Nama Rekanan" name="name" placeholder="Masukkan nama rekanan" />
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
        <form id="partner_form_delete" method="POST">
            @method('delete')
            @csrf
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('.partner_edit').on('click', function () {
                const partner = $(this).data('partner');
                $('#update_name').val(partner.name);
                $('#update_address').val(partner.address);
                $('#partner_edit_form').attr('action', `/rekanan/${partner.id}`);
            });

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
                        form.attr('action', `/rekanan/${id}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
