@extends('layouts.app')

@section('title', 'Order')

@section('content')
    <div class="dark:bg-mirage">
        <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h4 class="text-xl font-medium">Order</h4>
                <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data order</p>
            </div>
            <div class="inline-flex h-max rounded-sm" role="group">
                <a href="/order/form"
                    class="rounded-s-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500">
                    Tambah
                </a>
                <button type="button"
                    class="rounded-e-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500">
                    Export
                </button>
            </div>
        </div>

        <div class="dark:bg-mirage">
            <form class="mb-4 flex  w-full items-center gap-4" action="{{ route('order.view') }}" method="get">
                <div class="max-w-sm w-full">
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative flex-1">
                        <input value="{{ request('search') }}" type="text" id="search" name="search"
                            class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                            placeholder="Cari data order ..." />
                    </div>
                </div>
                <div>
                    <label for="alert" class="sr-only">Jenis alert</label>
                    <select id="alert" name="alert"
                        onchange="if(this.value === 'all' && !document.getElementById('search').value) { window.location.href='{{ route('order.view') }}'; return false; }"
                        class="w-max bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="all" {{ request('alert') == 'all' || !request('alert') ? 'selected' : '' }}>Semua
                        </option>
                        <option value="close" {{ request('alert') == 'close' ? 'selected' : '' }}>Close</option>
                        <option value="ltw" {{ request('alert') == 'ltw' ? 'selected' : '' }}>Kurang 7 hari</option>
                        <option value="mtw" {{ request('alert') == 'mtw' ? 'selected' : '' }}>Lebih 7 hari</option>
                    </select>
                </div>

                <button
                    onclick="if(document.getElementById('alert').value === 'all' && !document.getElementById('search').value) { window.location.href='{{ route('order.view') }}'; return false; }"
                    type="submit"
                    class="w-10 rounded-lg aspect-square border border-gray-300 bg-white p-2 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="sr-only">Search</span>
                </button>
                @if (request('search') || request('alert'))
                    <a href="{{ route('order.view') }}"
                        class="w-10 rounded-lg aspect-square border border-gray-300 bg-white p-2 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                        <i class="fa-regular fa-circle-xmark mt-1 text-center mx-auto block"></i>
                        <span class="sr-only">Clear</span>
                    </a>
                @endif
            </form>
        </div>

        <div class="relative overflow-x-auto sm:rounded-md">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-24 px-6 py-3">PO Id</th>
                        <th scope="col" class="px-6 py-3">Nomor PO</th>
                        <th scope="col" class="px-6 py-3">Rekanan</th>
                        <th scope="col" class="px-6 py-3">Tanggal PO</th>
                        <th scope="col" class="px-6 py-3">Tanggal Start</th>
                        <th scope="col" class="px-6 py-3">Tanggal Finish</th>
                        <th scope="col" class="px-6 py-3">Alert</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-24 px-6 py-4">{{ $order->id }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $order->po_number }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $order->partner->name }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $order->po_date }}</td>
                            <td class="min-w-[12em] px-6 py-4 start-date">{{ $order->start_date }}</td>
                            <td class="min-w-[12em] px-6 py-4 finish-date">{{ $order->finish_date }}</td>
                            <td class="min-w-[12em] px-6 py-4 status-cell"></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4">
                                    <a href="/order/{{ $order->id }}"
                                        class="edit-button font-semibold text-green-600 dark:text-green-400">
                                        <i class="fa-solid fa-circle-info text-base"></i>
                                    </a>
                                    <button class="order-delete-confirm font-semibold text-red-500"
                                        data-id="{{ $order->id }}">
                                        <i class="fa-solid fa-trash-can text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($orders->isEmpty())
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-clay px-6 py-4 text-center" colspan="8">Tidak ada data order</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
        <form id="order_form_delete" method="POST">
            @method('delete')
            @csrf
        </form>
    @endsection

    @section('scripts')
        <script>
            $(document).ready(function() {
                function calculateDays(targetDate) {
                    let today = new Date();
                    let target = new Date(targetDate);
                    let timeDiff = target.getTime() - today.getTime();
                    let daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    return daysDiff;
                }

                $('tr').each(function() {
                    const finishDateText = $(this).find('.finish-date').text();

                    if (finishDateText) {
                        const daysLeft = calculateDays(finishDateText);
                        if (daysLeft < 0) {
                            $(this).find('.status-cell').html(`
                                <span class="bg-red-600 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">Close</span>
                            `);

                        } else {
                            $(this).find('.status-cell').html(`
                                <span class="${daysLeft <= 7 && 'bg-yellow-400'} ${daysLeft > 7 &&  'bg-green-600'} text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded">${daysLeft} hari</span>
                            `);
                        }

                    }
                });

                $('.order-delete-confirm').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus data order tersebut (item/barang akan terhapus) ?',
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
                            const form = $('#order_form_delete');
                            form.attr('action', `/order/${id}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endsection
