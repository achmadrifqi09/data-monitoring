@extends('layouts.app')

@section('title', 'Detail BPL')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Detail BPL</h4>
        <div class="mt-6 bg-white border border-gray-200 dark:border-none dark:bg-clay p-4 rounded-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Nomor BPL :</p>
                    <p class="text-sm font-medium">{{ $bpl->bpl_number }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal Recana Pakai : </p>
                    <p class="text-sm font-medium">{{ $bpl->date_of_use ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Uraian : </p>
                    <p class="text-sm font-medium">{{ $bpl->description }}</p>
                </div>

            </div>
            <div class="mt-6 flex gap-2">
                @can('bpl_update')
                    <button type="button" data-modal-target="update-bpl" data-modal-toggle="update-bpl"
                        class="px-3 py-2 text-sm font-medium text-center border border-gray-300 dark:border-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        Update
                    </button>
                @endcan
                @can('bpl_delete')
                    <form action="/bpl/{{ $bpl->bpl_number }}" method="post" id="delete-bpl-form">
                        @method('DELETE')
                        @csrf
                        <button type="button" id="delete-bpl"
                            class="px-3 py-2 text-sm font-medium text-center border border-gray-300 dark:border-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="mt-8">
            <div class="flex justify-between items-center">
                <h4 class="font-semibold">Daftar Barang/Item</h4>
                @can('bpl_create')
                    <button data-modal-target="create-bpl-item" data-modal-toggle="create-bpl-item"
                        class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white">
                        Tambah Item
                    </button>
                @endcan
            </div>
            <div class="relative overflow-x-auto sm:rounded-md mt-4">
                <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                    <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                        <tr>
                            <th scope="col" class="w-24 px-6 py-3">Item Id</th>
                            <th scope="col" class="px-6 py-3">Nama Barang</th>
                            <th scope="col" class="px-6 py-3">Satuan</th>
                            <th scope="col" class="px-6 py-3">Merk</th>
                            <th scope="col" class="px-6 py-3">Spesifikasi</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            @if (auth()->user()->can('bpl_delete') || auth()->user()->can('bpl_update'))
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bpl->items as $item)
                            <tr class="bg-white hover:bg-gray-50 dark:bitem-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                                <td class="w-24 px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->item_name }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->unit ?? '-' }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->brand ?? '-' }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->specification ?? '-' }}</td>
                                <td class="min-w-[12em] px-6 py-4">
                                    @if ($item->is_selected == 1)
                                        <i class="fa-solid fa-circle-check text-green-600"></i>
                                    @else
                                        <i class="fa-solid fa-circle-xmark text-red-600"></i>
                                    @endif
                                </td>
                                @if (auth()->user()->can('bpl_delete') || auth()->user()->can('bpl_update'))
                                    <td class="px-6 py-4">
                                        <div class="flex gap-4">
                                            @can('bpl_update')
                                                <button
                                                    class="item-update-modal font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                                    data-modal-target="update-bpl-item" data-modal-toggle="update-bpl-item"
                                                    data-item="{{ json_encode($item) }}">
                                                    <i class="fa-solid fa-pen-to-square text-base"></i>
                                                </button>
                                            @endcan
                                            @can('bpl_delete')
                                                <button
                                                    class="delete-bpl-item font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                                    data-item-id="{{ $item->id }}"
                                                    data-bpl-number="{{ $item->bpl_number }}">
                                                    <i class="fa-solid fa-trash-can text-base"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr
                                class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                                <td class="bg-white dark:bg-clay  px-6 py-4 text-center"
                                    colspan="{{ auth()->user()->can('bpl_delete') || auth()->user()->can('bpl_update') ? 7 : 6 }}">
                                    Tidak ada data item/barang
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @can('bpl_create')
            <x-modal id="create-bpl-item" title="Tambah Item BPL">
                <x-slot name="content">
                    <form action="/bpl/{{ $bpl->bpl_number }}/item" method="post">
                        @csrf
                        <x-input-label id="item_name" label="Nama Item *" name="item_name" placeholder="Masukkan nama item"
                            required />
                        <x-input-label id="unit" label="Unit" name="unit" placeholder="Masukkan unit" />
                        <x-input-label id="brand" label="Merk" name="brand" placeholder="Masukkan merk" />
                        <x-input-label id="specification" label="Spesifikasi" name="specification"
                            placeholder="Masukkan spesifikasi" />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
        @can('bpl_update')
            <x-modal id="update-bpl-item" title="Update Item BPL">
                <x-slot name="content">
                    <form method="post" id="update-bpl-item-form">
                        @method('PATCH')
                        @csrf
                        <x-input-label id="item_name_update" label="Nama Item *" name="item_name"
                            placeholder="Masukkan nama item" required />
                        <x-input-label id="unit_update" label="Unit" name="unit" placeholder="Masukkan unit" />
                        <x-input-label id="brand_update" label="Merk" name="brand" placeholder="Masukkan merk" />
                        <x-input-label id="specification_update" label="Spesifikasi" name="specification"
                            placeholder="Masukkan spesifikasi" />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
        @can('bpl_update')
            <x-modal id="update-bpl" title="Update Item BPL">
                <x-slot name="content">
                    <form method="post" id="update-bpl-item-form" action="/bpl/{{ $bpl->id }}">
                        @method('PATCH')
                        @csrf
                        <x-input-label id="bpl_number" label="Nomor BPL" name="bpl_number" placeholder="Masukkan nomor BPL"
                            value="{{ $bpl->bpl_number }}" required min="1" />
                        <x-input-label id="description" label="Uraian *" name="description" placeholder="Masukkan uraian"
                            value="{{ $bpl->description }}" required />
                        <x-input-label type="date" id="date-of-use" label="Tanggal Rencana Pakai *" name="date_of_use"
                            value="{{ $bpl->date_of_use }}" placeholder="Masukkan tanggal rencana pakai" required />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
        @can('bpl_update')
            <form method="post" id="delete-bpl-item-form">
                @method('DELETE')
                @csrf
            </form>
        @endcan
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#delete-bpl').on('click', function() {
                const theme = localStorage.getItem('theme');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus data BPL terkait?',
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
                        const form = $('#delete-bpl-form')
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
            $('.item-update-modal').on('click', function() {
                const updatedData = $(this).data('item');
                if (updatedData) {
                    $('#item_name_update').val(updatedData.item_name);
                    $('#unit_update').val(updatedData.unit);
                    $('#brand_update').val(updatedData.brand);
                    $('#specification_update').val(updatedData.specification);
                    $('#update-bpl-item-form').attr('action',
                        `/bpl/${updatedData?.bpl_number}/item/${updatedData.id}`)
                }
            });
            $('.delete-bpl-item').on('click', function() {
                const theme = localStorage.getItem('theme');
                const deletedId = $(this).data('item-id');
                const bplNumber = $(this).data('bpl-number');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus data item terkait?',
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
                        const form = $('#delete-bpl-item-form')
                        form.attr('action', `/bpl/${bplNumber}/item/${deletedId}`)
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        })
    </script>
@endpush
