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
                <button type="button"
                    class="px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
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
                                    <button class="item-update-modal font-semibold text-yellow-400"
                                        data-modal-target="update-item" data-modal-toggle="update-item"
                                        data-item="{{ json_encode($item) }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button class="item-delete-confirm font-semibold text-red-500"
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
                            <td class="bg-clay px-6 py-4 text-center" colspan="7">Tidak ada data item/barang</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <x-modal id="update-item" title="Update Barang/Item">
                <x-slot name="content">
                    <form method="post" id="update-item-form">
                        @csrf
                        @method('PATCH')
                        <x-input-label id="item_name" label="Nama Item *" name="item_name"
                            placeholder="Masukkan nama item" />
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
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
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
    </script>
@endsection
