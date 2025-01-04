@extends('layouts.app')

@section('title', 'BPL')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">BPL</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data BPL</p>
        </div>
        @if (auth()->user()->can('bpl_create') || auth()->user()->can('bpl_export') || auth()->user()->can('bpl_import'))
            <div class="inline-flex h-max rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700"
                role="group">
                @can('bpl_create')
                    <button type="button" data-modal-target="create-bpl" data-modal-toggle="create-bpl"
                        class="bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
                        Tambah
                    </button>
                @endcan
                @can('bpl_export')
                    <button type="button"
                        class="border-l border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700  dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white ">
                        Export
                    </button>
                @endcan
                @can('bpl_import')
                    <button type="button" data-modal-target="import-bpl" data-modal-toggle="import-bpl"
                        class="border-l border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
                        Import
                    </button>
                @endcan
            </div>
        @endif
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
                    <th scope="col" class="px-6 py-3">No BPL</th>
                    <th scope="col" class="px-6 py-3">Jumlah Item</th>
                    <th scope="col" class="px-6 py-3">Uraian</th>
                    <th scope="col" class="px-6 py-3">Tanggal Rencana Pakai</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bpl as $bplItem)
                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="w-16 px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="min-w-[8em] px-6 py-4">{{ $bplItem->bpl_number }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ count($bplItem->items) }} item</td>
                        <td class="min-w-[12em] px-6 py-4">
                            {{$bplItem->description ?? '-'}}
                        </td>
                    <td class="min-w-[12em] px-6 py-4">{{ $bplItem->date_of_use ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <a href="/bpl/{{ $bplItem->id }}"
                                class="edit-button font-semibold text-gray-400 hover:dark:text-white hover:text-gray-600">
                                <i class="fa-solid fa-circle-info text-base"></i>
                            </a>
                            @can('bpl_delete')
                                <button data-bpl-number="{{ $bplItem->bpl_number }}"
                                    class="bpl-delete-confirm font-semibold text-gray-400 hover:dark:text-white hover:text-gray-600">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                    <td class="bg-white dark:bg-clay px-6 py-4 text-center" colspan="6">Tidak ada data BPL</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @can('bpl_import')
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
    @endcan
    @can('bpl_create')
        <x-modal id="create-bpl" title="Tambah BPL">
            <x-slot name="content">
                <form action="/bpl" method="post">
                    @csrf
                    <x-input-label id="bpl_number" label="Nomor BPL" name="bpl_number" placeholder="Masukkan nomor BPL"
                        required min="1" />
                    <x-input-label id="description" label="Uraian *" name="description" placeholder="Masukkan uraian"
                                   required />
                    <x-input-label type="date" id="date-of-use" label="Tanggal Rencana Pakai *" name="date_of_use"
                                   placeholder="Masukkan tanggal rencana pakai" required />
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
    @endcan
    @can('bpl_delete')
        <form id="bpl_form_delete" method="POST">
            @method('delete')
            @csrf
        </form>
    @endcan
</div>
<div class="mt-4">
    {{ $bpl->links() }}
</div>
@endsection

@push('scripts')
<script>
    $(function() {
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
                    const bplNumber = $(this).data('bpl-number');
                    const form = $('#bpl_form_delete');
                    form.attr('action', `/bpl/${bplNumber}`);
                    form.trigger('submit');
                    form.preventDefault();
                }
            });
        });
    })
</script>
@endpush
