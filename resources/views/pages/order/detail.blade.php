@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Detail Order</h4>
        <div class="mt-6 bg-white border border-gray-200 dark:border-none dark:bg-clay p-4 rounded-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Nomor PO</p>
                    <p class="text-sm font-medium">{{ $order->po_number }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Rekanan</p>
                    <p class="text-sm font-medium">{{ $order->partner->name }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal PO</p>
                    <p class="text-sm font-medium">{{ $order->po_date }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal Start</p>
                    <p class="text-sm font-medium">{{ $order->start_date }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal Finish</p>
                    <p class="text-sm font-medium finish-date">{{ $order->finish_date }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Status Alert</p>
                    <div class="text-sm font-medium" id="alert"></div>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                @if ($order->bpl->isEmpty())
                    <button type="button" disabled
                        class="px-3 py-2 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        Penerimaan
                    </button>
                @else
                    <a href="/item-received/form?order_id={{ $order->id }}"
                        class="px-3 py-2 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        Penerimaan
                    </a>
                @endif
                <button type="button" data-modal-target="order-document-modal" data-modal-toggle="order-document-modal"
                    class="px-3 py-2 text-sm font-medium text-center border border-gray-300 dark:border-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    Upload Dokumen
                </button>

                <button id="dropdownBackupDocs" data-dropdown-toggle="dropdown-backup-docs"
                    class="px-3 py-2 text-sm font-medium text-center border border-gray-300 dark:border-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600 flex gap-1 items-center"
                    type="button">
                    Dokumen
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div id="dropdown-backup-docs"
                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownBackupDocs">
                        @forelse ($order->order_backup_scans as $backup_scan)
                            <li class="flex justify-between px-2">
                                <a href="/document/order?path={{ $backup_scan->document }}" target="_blak"
                                    class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Dokumen Ke-{{ $loop->iteration }}
                                </a>
                                <button class="p-2 btn-delete-backup-doc" data-backup-scan-id="{{ $backup_scan->id }} ">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </li>
                        @empty
                            <li>
                                <span
                                    class="block p-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white select-none">
                                    Tidak ada dokumen
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <form method="post" id="delete-backup-scan-form">
            @method('delete')
            @csrf
        </form>
        <div class="flex justify-between items-center gap-6 mt-8 mb-4">
            <div>
                <h4 class="font-semibold">Daftar Barang/Item</h4>
                <p class="text-sm dark:text-gray-300">
                    Daftar barang/item dari BPL yang dipilih
                </p>
            </div>
            <a href="/order/{{ $order->id }}/bpl-form"
                class="bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white">
                Tambah BPL
            </a>
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
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->bpl as $BPLs)
                        @foreach ($BPLs->items as $item)
                            <tr
                                class="bg-white hover:bg-gray-50 dark:bitem-gray-700 dark:bg-clay dark:hover:bg-clay/80 item-rows">
                                <td class="w-24 px-6 py-4">{{ $item->id }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->item_name }}</td>
                                <td class="w-24 px-6 py-4">{{ $item->bpl_number }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $item->unit }}</td>
                                <td class="min-w-[12em] px-6 py-4 item-volume-cell">{{ $item->volume }}</td>
                                <td class="min-w-[12em] px-6 py-4 item-price-cell">{{ $item->price }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-4">
                                        <button
                                            class="item-update-modal font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                            data-modal-target="update-item" data-modal-toggle="update-item"
                                            data-item="{{ json_encode($item) }}">
                                            <i class="fa-solid fa-pen-to-square text-base"></i>
                                        </button>
                                        <button
                                            class="item-delete-confirm font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                            data-item-id="{{ $item->id }}">
                                            <i class="fa-solid fa-trash-can text-base"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white dark:bg-clay  px-6 py-4 text-center" colspan="7">
                                Tidak ada data item/barang
                            </td>
                        </tr>
                    @endforelse
                    @if (!$order->bpl->isEmpty())
                        <tr
                            class="bg-white hover:bg-gray-50 dark:bg-clay dark:hover:bg-clay/80 border-t border-t-gray-200/90 dark:border-t-gray-700">
                            <td class="w-24 px-6 py-4 font-semibold" colspan="5">Total Harga</td>
                            <td class="w-24 px-6 py-4 font-semibold" id="total-price-of-goods" colspan="2"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <x-modal id="update-item" title="Update Barang/Item">
            <x-slot name="content">
                <form method="post" id="update-item-form">
                    @csrf
                    @method('PATCH')
                    <x-input-label id="item_name" label="Nama Item *" name="item_name" placeholder="Masukkan nama item" />
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
        <form method="post" id="delete-item">
            @method('delete')
            @csrf
        </form>
        <div class="flex justify-between items-center gap-6 mt-8 mb-4">
            <div>
                <h4 class="font-semibold">Daftar Item Diterima</h4>
                <p class="text-sm dark:text-gray-300">
                    Daftar penerimaan item dari BPL yang dipilih
                </p>
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
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($itemReceiveds as $itemReceived)
                        <tr
                            class="bg-white hover:bg-gray-50 dark:bitem-gray-700 dark:bg-clay dark:hover:bg-clay/80 item-received-rows">
                            <td class="w-24 px-6 py-4">{{ $itemReceived->item_id }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->item->item_name }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->item->bpl_number }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->date_received }}</td>
                            <td class="px-6 py-4 amount_received item-received-volume-cell">
                                {{ $itemReceived->amount_received }}
                            </td>
                            <td class="px-6 py-4 amount_received item-received-price-cell">
                                {{ $itemReceived->nominal }}
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    class="recieved-item-delete-confirm font-semibold dark:text-gray-400 hover:dark:text-white text-gray-500"
                                    data-received-item-id="{{ $itemReceived->id }}">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white dark:bg-clay  px-6 py-4 text-center" colspan="7">
                                Tidak ada item/barang diterima
                            </td>
                        </tr>
                    @endforelse
                    @if (!$itemReceiveds->isEmpty())
                        <tr
                            class="bg-white hover:bg-gray-50 dark:bg-clay dark:hover:bg-clay/80 border-t border-t-gray-300 dark:border-t-gray-700">
                            <td class="w-24 px-6 py-4 font-semibold" colspan="5">Harga Total</td>
                            <td class="w-24 px-6 py-4 font-semibold" colspan="2" id="item-recived-total"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
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
                    <div class="mt-6 flex w-full justify-end p-0 box-border">
                        <x-button type="submit" class="mr-0 w-auto sm:mr-7">Submit</x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
    </div>

@endsection

@push('scripts')
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
                        const backupScanId = $(this).data('backup-scan-id')
                        const form = $('#delete-backup-scan-form');
                        form.attr('action', `/order/document/${backupScanId}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });

            })

            function priceFormatter(price) {
                price = price.replace(/[^0-9]/g, '');
                return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            let totalPrice = 0;
            let itemRecivedTotal = 0;
            $('.item-rows').each(function() {
                const priceElement = $(this).find('.item-price-cell');
                const volumenElement = $(this).find('.item-volume-cell');

                const price = priceElement.text();
                const volume = volumenElement.text();

                totalPrice += parseFloat(volume) * parseInt(price)

                const priceFormat = priceFormatter(price);
                priceElement.text(`Rp ${priceFormat}`);
            });

            $('.item-received-rows').each(function() {
                const priceElement = $(this).find('.item-received-price-cell');
                const price = priceElement.text();

                itemRecivedTotal += parseInt(price)

                const priceFormat = priceFormatter(price);
                priceElement.text(`Rp ${priceFormat}`);
            });

            const totalPriceFormatted = priceFormatter(totalPrice.toString());
            $('#total-price-of-goods').text(`Rp ${totalPriceFormatted}`);

            const itemRecivedTotalFormatted = priceFormatter(itemRecivedTotal.toString());
            $('#item-recived-total').text(`Rp ${itemRecivedTotalFormatted}`);

            const today = new Date().toISOString().split('T')[0]
            $('#received_date_volume_0').val(today)
            $('.item-update-modal').on('click', function() {
                const data = $(this).data('item');
                if (data) {
                    $('#update-item-form').attr('action',
                        `/order/${@json($order->id)}/items/${data.id}`);
                    $('#item_name').val(data.item_name);
                    $('#volume').val(data.volume);
                    $('#price').val(data.price);
                }
            })

            function calculateDays(targetDate) {
                let today = new Date();
                let todayInJakarta = new Date(new Intl.DateTimeFormat('en-US', {
                    timeZone: 'Asia/Jakarta'
                }).format(today));
                let target = new Date(new Intl.DateTimeFormat('en-US', {
                    timeZone: 'Asia/Jakarta'
                }).format(new Date(targetDate)));
                let timeDiff = target.getTime() - todayInJakarta.getTime();
                let daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

                return daysDiff;
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
                                <span class="${daysLeft <= 7 && 'bg-yellow-400'} ${daysLeft > 7 &&  'bg-green-600'} text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">${daysLeft} hari</span>
                            `);
                }

            }

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

            $('#item_id').select2({
                ajax: {
                    url: '{{ route('bpl.api.get') }}',
                    delay: 350,
                    dataType: 'json',
                    data: function(params) {
                        const query = {
                            search: params.term,
                        }
                        return query;
                    },
                    processResults: function(data) {
                        const result = data?.map((bpl) => {
                            return {
                                id: bpl.id,
                                text: `${bpl.item_name} - ${bpl.unit}`
                            }
                        })
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        })

        let counter = 1;
        const items = @json($order->items);
        $('#add-field').on('click', function() {
            const today = new Date().toISOString().split('T')[0]
            const newRow = `
            <div class="sm:flex gap-4 grid grid-cols-2 mt-4 mb-6" id="${counter}">
                <div class="w-full">
                     <label for="received_id_${counter}"
                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Barang/Item*
                    </label>
                    <select id="received_id_${counter}" name="received_items[${counter}][bpl_id]" required
                        class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-600">
                        ${items.map((item) => {
                            return `<option value="${item.id}">${item.item_name} - Volume ${item.volume}</option>`
                        }).join('')}
                    </select>
                </div>
                <div class="sm:w-max">
                    <x-input-label for="received_volume_${counter}}" :isSpaceY="false" id="received_volume_${counter}"
                        type="number" min="1" label="Jumlah *"
                        name="received_items[${counter}][amount_received]" class="sm:w-40" placeholder="Vol diterima"
                        required />
                </div>
                <div class="sm:w-max">
                    <x-input-label for="received_date_${counter}" :isSpaceY="false" id="received_date_volume_${counter}"
                        type="date" label="Tanggal Diterima *" name="received_items[${counter}][received_date]"
                        class="sm:w-40" placeholder="Tanggal diterima" required value="${today}" />
                </div>
                <button data-row-id="${counter}" class="remove-field w-6 h-[50px] relative" type="button">
                    <i class="fa-regular fa-circle-xmark absolute -bottom-2 sm:-left-2"></i>
                </button>
            </div>
            `;
            $('#additional-field').append(newRow);
            counter++;

            $('.remove-field').on('click', function() {
                const rowId = $(this).data('row-id')
                $(`#${rowId}`).remove()
            })
        });
        $('.recieved-item-delete-confirm').on('click', function() {
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
        })
    </script>
@endpush
