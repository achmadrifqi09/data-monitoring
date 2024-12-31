@extends('layouts.app')

@section('title', 'BPL')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">BPL</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data BPL</p>
        </div>
        <div class="inline-flex h-max rounded-sm" role="group">
            <button type="button" data-modal-target="create-bpl" data-modal-toggle="create-bpl"
                class="rounded-s-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500">
                Tambah
            </button>
            <button type="button"
                class="border-b border-t border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500">
                Export
            </button>
            <button type="button" data-modal-target="import-bpl" data-modal-toggle="import-bpl"
                class="rounded-e-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500">
                Import
            </button>
        </div>
    </div>

    <div>
        <form class="mb-4 flex max-w-sm items-center" action="{{ route('bpl.view') }}" method="get">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative flex-1">
                <input value="{{ request('search') }}" type="text" id="search" name="search"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                    placeholder="Cari data bpl ..." />
                @if (request('search'))
                    <a href="{{ route('bpl.view') }}"
                        class="absolute right-0.5 top-0.5 z-10 rounded-md bg-gray-50 p-2 dark:bg-gray-700">
                        <i class="fa-regular fa-circle-xmark"></i>
                        <span class="sr-only">Clear</span>
                    </a>
                @endif
            </div>
            <button type="submit"
                class="ms-3 rounded-lg w-10 aspect-square border border-gray-300 bg-white p-2.5 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
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
                    <th scope="col" class="px-6 py-3">Nama Item</th>
                    <th scope="col" class="px-6 py-3">Satuan</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bpl as $item)
                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="w-16 px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="min-w-[8em] px-6 py-4">{{ $item->item_name }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ $item->unit }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-4">
                                <button
                                    class="edit-button font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                    data-modal-target="edit-bpl" data-modal-toggle="edit-bpl"
                                    data-partner="{{ json_encode($item) }}">
                                    <i class="fa-solid fa-pen-to-square text-base"></i>
                                </button>
                                <button
                                    class="bpl-delete-confirm font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                    data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($bpl->isEmpty())
                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="bg-white dark:bg-clay px-6 py-4 text-center" colspan="4">Tidak ada data BPL</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <x-modal id="import-bpl" title="Import BPL">
            <x-slot name="content">
                <form action="/bpl/import" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="relative">
                        <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Pilih file excel
                        </label>
                        <input id="excel_file" type="file" name="excel_file"
                            class="w-full rounded-md border border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-300" />
                    </div>
                    <p class="mt-1 text-sm font-medium italic text-gray-500 dark:text-gray-300">
                        *Pastikan nama item BPL tidak ada yang kosong, jika nama rekanan kosong akan dilewati pada baris
                        tersebut
                    </p>
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
        <x-modal id="create-bpl" title="Tambah BPL">
            <x-slot name="content">
                <form action="/bpl" method="post">
                    @csrf
                    <x-input-label id="item_name" label="Nama Item" name="item_name" placeholder="Masukkan nama item" />
                    <x-input-label id="unit" label="Satuan" name="unit" placeholder="Masukkan satuan item" />
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
        <x-modal id="edit-bpl" title="Update BPL">
            <x-slot name="content">
                <form method="post" id="edit_form">
                    @method('patch')
                    @csrf
                    <x-input-label id="update_item_name" label="Nama Unit" name="item_name"
                        placeholder="Masukkan nama item" />
                    <x-input-label id="update_unit" label="Satuan" name="unit" placeholder="Masukkan satuan item" />
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
        <form id="bpl_form_delete" method="POST">
            @method('delete')
            @csrf
        </form>
    </div>
    <div class="mt-4">
        {{ $bpl->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.edit-button').on('click', function() {
                const partner = $(this).data('partner');
                $('#update_item_name').val(partner.item_name);
                $('#update_unit').val(partner.unit);
                $('#edit_form').attr('action', `/bpl/${partner.id}`);
            });
            $('.bpl-delete-confirm').on('click', function() {
                const theme = localStorage.getItem('theme');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus data BPL tersebut?',
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
                        const form = $('#bpl_form_delete');
                        form.attr('action', `/bpl/${id}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        })
    </script>
@endpush
