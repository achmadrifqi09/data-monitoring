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
            <div class="mt-6 space-x-2">
                <button type="button" @disabled($order->items->isEmpty()) data-modal-target="goods-receipt"
                    data-modal-toggle="goods-receipt"
                    class="px-3 py-2 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    Penerimaan
                </button>
                <button type="button"
                    class="px-3 py-2 text-sm font-medium text-center border border-gray-300 dark:border-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-500 dark:bg-clay dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    Invoice
                </button>
            </div>
        </div>

        <div class="flex justify-between items-center gap-6 mt-8 mb-4">
            <h4 class="font-semibold">Daftar Barang/Item</h4>
            <button data-modal-target="add-item" data-modal-toggle="add-item"
                class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white">
                Tambah Item
            </button>
        </div>
        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-24 px-6 py-3">Item Id</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-6 py-3">Satuan</th>
                        <th scope="col" class="px-6 py-3">Volume Kontrak</th>
                        <th scope="col" class="px-6 py-3">Harga</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="bg-white hover:bg-gray-50 dark:bitem-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4">{{ $item->id }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $item->item_name }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $item->unit }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $item->volume }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $item->price }}</td>
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

                    @if ($order->items->isEmpty())
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white dark:bg-clay  px-6 py-4 text-center" colspan="7">Tidak ada data
                                item/barang</td>
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
        <x-modal id="add-item" title="Tambah Barang/Item">
            <x-slot name="content">
                <form method="post" id="add-item-form" action="/order/{{ $order->id }}/items">
                    @csrf
                    <div class="w-full text-mirage">
                        <label for="items" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Item(BPL) *
                        </label>
                        <select class="items" id="item_id" name="item_id"></select>
                    </div>
                    <input type="number" value="{{ $order->partner_id }}" name="partner_id" hidden>
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
            <h4 class="font-semibold">Daftar Barang/Item Diterima</h4>
        </div>
        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-24 px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-6 py-3">Volume Diterima</th>
                        <th scope="col" class="px-6 py-3">Tanggal Diterima</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($itemReceiveds as $itemReceived)
                        <tr class="bg-white hover:bg-gray-50 dark:bitem-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $itemReceived->item->item_name }}</td>
                            <td class="px-6 py-4">
                                {{ $itemReceived->amount_received }}
                                {{ $itemReceived->item->unit }}
                            </td>
                            <td class="px-6 py-4">{{ $itemReceived->date_received }}</td>
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
                </tbody>
            </table>
        </div>
        <x-modal id="goods-receipt" title="Penerimaan Barang/Item">
            <x-slot name="content">
                <button id="add-field"
                    class="bg-gray-100 mb-4 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white">
                    Tambah Field
                </button>
                <form method="post" id="goods-receipt-form" action="/item-received">
                    @csrf
                    <input type="number" name="order_id" hidden value="{{ $order->id }}">
                    <div class="sm:flex gap-4 grid grid-cols-2 mt-4 mb-6 sm:mr-7">
                        <div class="w-full">
                            <label for="received_id_0"
                                class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Barang/Item*
                            </label>
                            <select id="received_id_0" name="received_items[0][bpl_id]" required
                                class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-600 focus:border-red-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-600 dark:focus:border-red-600">
                                @foreach ($order->items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->item_name }} - Volume {{ $item->volume }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-max">
                            <x-input-label for="received_volume_0" :isSpaceY="false" id="received_volume_0"
                                type="number" min="1" label="Jumlah *" name="received_items[0][amount_received]"
                                class="w-40" placeholder="Vol diterima" required />
                        </div>
                        <div class="w-max">
                            <x-input-label for="received_date_0" :isSpaceY="false" id="received_date_volume_0"
                                type="date" label="Tanggal Diterima *" name="received_items[0][received_date]"
                                class="w-40" placeholder="Tanggal diterima" required />
                        </div>
                    </div>
                    <div id="additional-field"></div>

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
                let target = new Date(targetDate);
                let timeDiff = target.getTime() - today.getTime();
                let daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                return daysDiff;
            }

            const finishDateText = $(this).find('.finish-date').text();

            if (finishDateText) {
                const daysLeft = calculateDays(finishDateText);
                if (daysLeft < 0) {
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
                            return `
                                <option value="${item.id}">
                                    ${item.item_name} - Volume ${item.volume}
                                </option>
                            `
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
