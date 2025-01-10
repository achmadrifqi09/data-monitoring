@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Detail Tagihan</h4>
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-none dark:bg-clay">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nomor PO</p>
                    <p class="text-sm font-medium">{{ $bill->order->po_number }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nomor BAP</p>
                    <p class="text-sm font-medium">{{ $bill->bap }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Tanggal BAP</p>
                    <p class="text-sm font-medium">{{ $bill->date_of_bap }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Rekanan</p>
                    <p class="text-sm font-medium">{{ $bill->order->partner->name }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">DPP</p>
                    <p class="text-sm font-medium price">{{ $bill->dpp }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Netto</p>
                    <p class="text-sm font-medium price">{{ $bill->netto }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">PPN</p>
                    <p class="text-sm font-medium">{{ $bill->ppn_percentage }}%</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nominal PPN</p>
                    <p class="text-sm font-medium price">{{ $bill->ppn }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Retensi</p>
                    <p class="text-sm font-medium price">{{ $bill->retention }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">PPH</p>
                    <p class="text-sm font-medium">{{ $bill->pph_percentage }}%</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nominal PPH</p>
                    <p class="text-sm font-medium price">{{ $bill->pph }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Tanggal Kuitansi</p>
                    <p class="finish-date text-sm font-medium" id="receipt_date" data-due-date="{{ $bill->due_date }}">
                        {{ $bill->receipt_date }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Status Alert</p>
                    <div class="text-sm font-medium" id="alert"></div>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                @can('bill_update')
                    <button type="button" data-modal-target="update-bill-modal" data-modal-toggle="update-bill-modal"
                        class="rounded-lg bg-red-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-600 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        Update
                    </button>
                    <x-modal id="update-bill-modal" title="Update Tagihan">
                        <x-slot name="content">
                            <form method="post" id="bill-item-update">
                                @method('PATCH')
                                @csrf
                                <div>
                                    <x-input-label id="bap" name="bap" label="BAP *" value="{{ $bill->bap }}"
                                        required />
                                    <x-input-label id="date_of_bap" name="date_of_bap" label="Tanggal BAP *" type="date"
                                        value="{{ $bill->date_of_bap }}" required />
                                    <x-input-label id="fee_deduction" name="fee_deduction" label="Potongan *" type="number"
                                        value="{{ $bill->fee_deduction }}" required />
                                    <x-input-label id="retention" name="retention" label="Retensi *" type="number"
                                        value="{{ $bill->retention }}" required />
                                    <x-input-label id="ppn" name="ppn" label="PPN(%) *" type="number"
                                        value="{{ $bill->ppn_percentage }}" required />
                                    <x-input-label id="pph" name="pph" label="PPH(%) *" type="number"
                                        value="{{ $bill->pph_percentage }}" required />
                                    <x-input-label id="receipt_date" name="receipt_date" label="Tanggal Kuitansi*"
                                        type="date" value="{{ $bill->receipt_date }}" required />
                                    <x-input-label id="due_date" name="due_date" label="Jatuh Tempo*" type="number"
                                        value="{{ $bill->due_date }}" required />
                                </div>
                                <div class="mt-6 flex w-full justify-end p-0">
                                    <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                                </div>
                            </form>
                        </x-slot>
                    </x-modal>
                @endcan
                @can('bill_create')
                    <div class="flex gap-2">
                        <button type="button" data-modal-target="order-document-modal" data-modal-toggle="order-document-modal"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-center text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-500 dark:border-gray-700 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            Upload Dokumen
                        </button>
                    </div>
                    <x-modal id="order-document-modal" title="Upload Dokumen Tagihan">
                        <x-slot name="content">
                            <form method="post" action="/bill/{{ $bill->id }}/document" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf
                                <div class="relative">
                                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        Pilih Dokumen (PDF)*
                                    </label>
                                    <input id="bill_document" type="file" name="bill_document" accept="application/pdf"
                                        class="w-full rounded-md border border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-300" />
                                </div>
                                <div class="mt-6 box-border flex w-full justify-end p-0">
                                    <x-button type="submit" class="mr-0 w-auto sm:mr-7">Submit</x-button>
                                </div>
                            </form>
                        </x-slot>
                    </x-modal>
                @endcan

                <button id="dropdownBackupDocs" data-dropdown-toggle="dropdown-backup-docs"
                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-center text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-500 dark:border-gray-700 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    type="button">
                    Dokumen
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div id="dropdown-backup-docs"
                    class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownBackupDocs">
                        @forelse ($bill->documents as $billDocument)
                            <li class="flex justify-between px-2">
                                <a href="/document/bill?path={{ $billDocument->document }}" target="_blak"
                                    class="block w-full rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Dokumen Ke-{{ $loop->iteration }}
                                </a>
                                @can('bill_delete')
                                    <button class="btn-delete-document p-2" data-bill-document-id="{{ $billDocument->id }} ">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                @endcan
                            </li>
                        @empty
                            <li>
                                <span
                                    class="block select-none rounded p-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Tidak ada dokumen
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <h4 class="text-lg font-medium mt-8 mb-2">Tagihan Item</h4>
        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-16 px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Item</th>
                        <th scope="col" class="px-6 py-3">Jml Item Ditagih</th>
                        <th scope="col" class="px-6 py-3">Harga Peritem</th>
                        <th scope="col" class="px-6 py-3">Total</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bill->bill_items as $item)
                        <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80"
                            id="rows">
                            <td class="w-16 px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="min-w-[8em] px-6 py-4">{{ $item->item->item_name }}</td>
                            <td class="min-w-[8em] px-6 py-4">
                                <span>{{ $item->total_item_billed }}</span>
                            </td>
                            <td class="min-w-[8em] px-6 py-4 price">{{ $item->item->order_item->price }}</td>
                            <td class="min-w-[12em] px-6 py-4 price-cell">
                                {{ (int) $item->item->order_item->price * (int) $item->total_item_billed }}</td>

                            <td class="px-6 py-4">
                                <div class="flex gap-4">
                                    @can('bill_update')
                                        <button
                                            class="btn-bill-item-update font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                            data-modal-target="update-bill-item" data-modal-toggle="update-bill-item"
                                            data-bill-item="{{ json_encode($item) }}">
                                            <i class="fa-solid fa-pen-to-square text-base"></i>
                                        </button>
                                    @endcan
                                    @can('bill_delete')
                                        <button data-bill-item-id="{{ $item->id }}" data-bill-id="{{ $item->bill_id }}"
                                            class="delete-bill-item font-semibold text-gray-400 hover:text-gray-600 hover:dark:text-white">
                                            <i class="fa-solid fa-trash-can text-base"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="6">Tidak ada data BPL</td>
                        </tr>
                    @endforelse
                    <tr
                        class="border-t border-t-gray-300 bg-white hover:bg-gray-50 dark:border-t-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="w-24 px-6 py-4 font-semibold" colspan="4">Total</td>
                        <td class="w-24 px-6 py-4 font-semibold" colspan="2" id="total-item-bill"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @can('bill_update')
        <x-modal id="update-bill-item" title="Update Tagihan Item">
            <x-slot name="content">
                <form method="post" id="bill-item-update">
                    @method('PATCH')
                    @csrf
                    <input type="text" id="updated-item-price" name="price" hidden>
                    <x-input-label id="updated-total-item-billed" name="total_item_billed" label="Jumlah item ditagih*"
                        required />
                    <div class="mt-6 flex w-full justify-end p-0">
                        <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
    @endcan

    @can('bill_delete')
        <form method="post" id="delete-bill-item">
            @method('DELETE')
            @csrf
        </form>
        <form method="post" id="delete-bil-document-form">
            @method('delete')
            @csrf
        </form>
    @endcan
@endsection

@push('scripts')
    <script>
        $(function() {
            const dateString = $('#receipt_date').text();
            const dueDate = $('#receipt_date').data('due-date');

            const dueDateWithAddedDays = moment.tz(dateString, 'Asia/Jakarta').add(dueDate, 'days');
            const currentDate = moment.tz('Asia/Jakarta');

            if (currentDate.isAfter(dueDateWithAddedDays)) {
                $('#alert').append(`
                 <span class="bg-red-600 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">Close</span>
                `);
            } else {
                const daysDifference = dueDateWithAddedDays.diff(currentDate, 'days');
                $('#alert').append(`
                 <span class="${daysDifference <= 7 && 'bg-yellow-400'} ${daysDifference > 7 && 'bg-green-600'} text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">${daysDifference} hari</span>
                `);
            };

            function priceFormatter(price) {
                price = price.replace(/[^0-9]/g, '');
                return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            $('.price').each(function() {
                const price = priceFormatter($(this).text());
                $(this).text(`Rp ${price}`);
            });

            let totalItemBill = 0;
            $('.price-cell').each(function() {
                const price = priceFormatter($(this).text());
                totalItemBill += parseInt($(this).text());

                $(this).text(`Rp ${price}`);
                $('#total-item-bill').text(`Rp ${priceFormatter(totalItemBill.toString())}`);
            });
        })
    </script>

    @can('bill_update')
        <script>
            $(function() {
                $('.btn-bill-item-update').on('click', function() {
                    const billItem = $(this).data('bill-item');

                    if (billItem) {
                        $('#bill-item-update').attr('action',
                            `/bill/${billItem.bill_id}/bill_item/${billItem.item_id}`);
                        $('#updated-item-price').val(billItem.item.order_item.price);
                        $('#updated-total-item-billed').val(billItem.total_item_billed);
                    }
                })
            })
        </script>
    @endcan

    @can('bill_delete')
        <script>
            $(function() {
                $('.delete-bill-item').on('click', function() {
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
                            const billItemId = $(this).data('bill-item-id');
                            const billId = $(this).data('bill-id');
                            const form = $('#delete-bill-item');
                            form.attr('action', `/bill/${billId}/bill_item/${billItemId}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
            })
            $('.btn-delete-document').on('click', function() {
                const theme = localStorage.getItem('theme');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin akan menghapus dokumen tagihan terkait?',
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
                        const documentId = $(this).data('bill-document-id');
                        const form = $('#delete-bil-document-form');
                        form.attr('action', `/bill/${documentId}/document`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        </script>
    @endcan
@endpush
