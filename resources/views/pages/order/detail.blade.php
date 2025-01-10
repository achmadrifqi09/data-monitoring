@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Detail Order</h4>
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-none dark:bg-clay">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nomor PO</p>
                    <p class="text-sm font-medium">{{ $order->po_number }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Rekanan</p>
                    <p class="text-sm font-medium">{{ $order->partner->name }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Tanggal PO</p>
                    <p class="text-sm font-medium">{{ $order->po_date }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Tanggal Start</p>
                    <p class="text-sm font-medium">{{ $order->start_date }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Tanggal Finish</p>
                    <p class="finish-date text-sm font-medium">{{ $order->finish_date }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Status Alert</p>
                    <div class="text-sm font-medium" id="alert"></div>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                @can('order_create')
                    <div class="flex gap-2">
                        @if ($order->order_items->isEmpty())
                            <button type="button" disabled
                                class="rounded-lg bg-red-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-600 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                Penerimaan
                            </button>
                        @else
                            <a href="/item-received/form?order_id={{ $order->id }}"
                                class="rounded-lg bg-red-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-600 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                Penerimaan
                            </a>
                        @endif
                        <button type="button" data-modal-target="order-document-modal" data-modal-toggle="order-document-modal"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-center text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-500 dark:border-gray-700 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            Upload Dokumen
                        </button>
                    </div>
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
                        @forelse ($order->order_backup_scans as $backup_scan)
                            <li class="flex justify-between px-2">
                                <a href="/document/order?path={{ $backup_scan->document }}" target="_blak"
                                    class="block w-full rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Dokumen Ke-{{ $loop->iteration }}
                                </a>
                                @can('order_delete')
                                    <button class="btn-delete-backup-doc p-2" data-backup-scan-id="{{ $backup_scan->id }} ">
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

        <div class="mb-4 mt-8 flex items-center justify-between gap-6">
            <div>
                <h4 class="font-semibold">Daftar Barang/Item</h4>
                <p class="text-sm dark:text-gray-300">Daftar barang/item dari BPL yang dipilih</p>
            </div>
            @can('order_create')
                <a href="/order/{{ $order->id }}/bpl-form"
                    class="me-2 rounded bg-gray-100 px-2.5 py-0.5 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-white">
                    Tambah BPL
                </a>
            @endcan
        </div>
        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-24 px-6 py-3">Item Id</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="w-24 px-6 py-3">No BPL</th>
                        <th scope="col" class="px-6 py-3">Satuan</th>
                        <th scope="col" class="px-6 py-3">Volume Kontrak</th>
                        <th scope="col" class="px-6 py-3">Harga</th>
                        <th scope="col" class="px-6 py-3">Harga Total Peritem</th>
                        @if (auth()->user()->can('order_update') || auth()->user()->can('order_delete'))
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->order_items as $orderItem)
                        <tr class="item-rows bg-white hover:bg-gray-50 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4">{{ $orderItem->item_id }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $orderItem->item->item_name ?? '-' }}</td>
                            <td class="w-24 px-6 py-4">{{ $orderItem->item->bpl_number ?? '-' }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $orderItem->item->unit ?? '-' }}</td>
                            <td class="item-volume-cell min-w-[12em] px-6 py-4">{{ $orderItem->volume }}</td>
                            <td class="item-price-cell min-w-[12em] px-6 py-4">{{ $orderItem->price }}</td>
                            <td class="item-price-total-cell min-w-[12em] px-6 py-4">
                                {{ $orderItem->price * $orderItem->volume }}
                            </td>
                            @if (auth()->user()->can('order_update') || auth()->user()->can('order_delete'))
                                <td class="px-6 py-4">
                                    <div class="flex gap-4">
                                        @can('order_update')
                                            <button
                                                class="item-update-modal font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                                data-modal-target="update-item" data-modal-toggle="update-item"
                                                data-item="{{ json_encode($orderItem) }}">
                                                <i class="fa-solid fa-pen-to-square text-base"></i>
                                            </button>
                                        @endcan

                                        @can('order_delete')
                                            <button
                                                class="item-delete-confirm font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                                data-item-id="{{ $orderItem->item_id }}">
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
                            <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="8">
                                Tidak ada data item/barang
                            </td>
                        </tr>
                    @endforelse
                    <tr
                        class="border-t border-t-gray-200/90 bg-white hover:bg-gray-50 dark:border-t-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="w-24 px-6 py-4 font-semibold" colspan="6">Total Harga</td>
                        <td class="w-24 px-6 py-4 font-semibold" id="total-price-of-goods" colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mb-4 mt-8 flex items-center justify-between gap-6">
            <div>
                <h4 class="font-semibold">Daftar Item Diterima</h4>
                <p class="text-sm dark:text-gray-300">Daftar penerimaan item dari BPL yang dipilih</p>
            </div>
        </div>
        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-24 px-6 py-3">Item Id</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-6 py-3">No BPL</th>
                        <th scope="col" class="px-6 py-3">Tanggal Diterima</th>
                        <th scope="col" class="px-6 py-3">Volume Diterima</th>
                        <th scope="col" class="px-6 py-3">Harga Total Peritem</th>
                        @if (auth()->user()->can('order_delete'))
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($itemReceiveds as $itemReceived)
                        <tr class="item-received-rows bg-white hover:bg-gray-50 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4">{{ $itemReceived->item_id ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->item->item_name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->item->bpl_number ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->date_received ?? '-' }}</td>
                            <td class="amount_received item-received-volume-cell px-6 py-4">
                                {{ $itemReceived->amount_received }}
                            </td>
                            <td class="amount_received item-received-price-cell px-6 py-4">
                                {{ $itemReceived->nominal }}
                            </td>
                            @can('order_delete')
                                <td class="px-6 py-4">
                                    <button
                                        class="received-item-delete-confirm font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                        data-received-item-id="{{ $itemReceived->id }}">
                                        <i class="fa-solid fa-trash-can text-base"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="7">
                                Tidak ada item/barang diterima
                            </td>
                        </tr>
                    @endforelse
                    @if (!$itemReceiveds->isEmpty())
                        <tr
                            class="border-t border-t-gray-300 bg-white hover:bg-gray-50 dark:border-t-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4 font-semibold" colspan="5">Harga Total</td>
                            <td class="w-24 px-6 py-4 font-semibold" colspan="2" id="item-received-total"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @can('order_update')
            <x-modal id="update-item" title="Update Barang/Item">
                <x-slot name="content">
                    <form method="post" id="update-item-form">
                        @csrf
                        @method('PATCH')
                        <x-input-label id="volume" type="number" label="Volume *" name="volume"
                            placeholder="Masukkan volume item" />
                        <x-input-label id="price" type="number" label="Harga *" name="price"
                            placeholder="Masukkan harga item" />
                        <div class="mt-6 flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan

        @can('order_delete')
            <form method="post" id="delete-item">
                @method('delete')
                @csrf
            </form>
            <form method="post" id="delete-backup-scan-form">
                @method('delete')
                @csrf
            </form>
        @endcan

        @can('order_create')
            <x-modal id="order-document-modal" title="Upload Dokumen Order">
                <x-slot name="content">
                    <form method="post" id="goods-receipt-form" action="/order/{{ $order->id }}/document"
                        enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        <div class="relative">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Pilih Dokumen (PDF)*
                            </label>
                            <input id="order_document" type="file" name="order_document" accept="application/pdf"
                                class="w-full rounded-md border border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-300" />
                        </div>
                        <div class="mt-6 box-border flex w-full justify-end p-0">
                            <x-button type="submit" class="mr-0 w-auto sm:mr-7">Submit</x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
    </div>
@endsection

@push('scripts')
    @can('order_delete')
        <script>
            $(function() {
                $('.btn-delete-backup-doc').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus dokumen order terkait?',
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
                            const backupScanId = $(this).data('backup-scan-id');
                            const form = $('#delete-backup-scan-form');
                            form.attr('action', `/order/document/${backupScanId}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
                $('.item-delete-confirm').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus data item tersebut?',
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
                            const id = $(this).data('item-id');
                            const form = $('#delete-item');
                            form.attr('action', `/order/${@json($order->id)}/items/${id}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
                $('.received-item-delete-confirm').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus data barang/item diterima tersebut?',
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
                            const id = $(this).data('received-item-id');
                            const form = $('#delete-item');
                            form.attr('action', `/item-received/${id}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endcan

    @can('order_update')
        <script>
            $(function() {
                $('.item-update-modal').on('click', function() {
                    const data = $(this).data('item');
                    if (data) {
                        $('#update-item-form').attr('action',
                            `/order/${@json($order->id)}/items/${data.item_id || 0}`);
                        $('#volume').val(data.volume);
                        $('#price').val(data.price);
                    }
                });
            });
        </script>
    @endcan

    <script>
        $(function() {
            function priceFormatter(price) {
                price = price.replace(/[^0-9]/g, '');
                return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            let totalPrice = 0;
            let itemReceivedTotal = 0;
            $('.item-rows').each(function() {
                const priceElement = $(this).find('.item-price-cell');
                const volumeElement = $(this).find('.item-volume-cell');
                const totalPerItemElement = $(this).find('.item-price-total-cell');

                const price = priceElement.text();
                const volume = volumeElement.text();
                const totalPerItem = totalPerItemElement.text();

                totalPrice += parseFloat(volume) * parseInt(price);

                const priceFormat = priceFormatter(price);
                const totalPerItemFormated = priceFormatter(totalPerItem);
                priceElement.text(`Rp ${priceFormat}`);
                totalPerItemElement.text(`Rp ${totalPerItemFormated}`);
            });

            $('.item-received-rows').each(function() {
                const priceElement = $(this).find('.item-received-price-cell');
                const price = priceElement.text();

                itemReceivedTotal += parseInt(price);

                const priceFormat = priceFormatter(price);
                priceElement.text(`Rp ${priceFormat}`);
            });

            const totalPriceFormatted = priceFormatter(totalPrice.toString());
            $('#total-price-of-goods').text(`Rp ${totalPriceFormatted}`);

            const itemReceivedTotalFormatted = priceFormatter(itemReceivedTotal.toString());
            $('#item-received-total').text(`Rp ${itemReceivedTotalFormatted}`);

            const today = new Date().toISOString().split('T')[0];
            $('#received_date_volume_0').val(today);

            function calculateDays(targetDate) {
                let today = new Date();
                let todayInJakarta = new Date(
                    new Intl.DateTimeFormat('en-US', {
                        timeZone: 'Asia/Jakarta',
                    }).format(today),
                );
                let target = new Date(
                    new Intl.DateTimeFormat('en-US', {
                        timeZone: 'Asia/Jakarta',
                    }).format(new Date(targetDate)),
                );
                let timeDiff = target.getTime() - todayInJakarta.getTime();
                return Math.ceil(timeDiff / (1000 * 3600 * 24));
            }

            const finishDateText = $(this).find('.finish-date').text();

            if (finishDateText) {
                const daysLeft = calculateDays(finishDateText);
                if (daysLeft <= 0) {
                    $(this).find('#alert').html(`
                                <span class="bg-red-600 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">Close</span>
                            `);
                } else {
                    $(this).find('#alert').html(`
                                <span class="${daysLeft <= 7 && 'bg-yellow-400'} ${daysLeft > 7 && 'bg-green-600'} text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">${daysLeft} hari</span>
                            `);
                }
            }
        });
    </script>
@endpush
