@extends('layouts.app')

@section('title', 'Tagihan')

@section('content')
    <div>
        <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h4 class="text-xl font-medium">Tagihan</h4>
                <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data tagihan</p>
            </div>
            @if (auth()->user()->can('bill_create'))
                <div class="inline-flex h-max overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
                    role="group">
                    @can('bill_create')
                        <a href="/bill/c/form"
                            class="bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
                            Tambah
                        </a>
                    @endcan
                </div>
            @endif
        </div>
        <div>
            <form class="mb-4 flex max-w-sm items-center" action="{{ route('bill.view') }}" method="get">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative flex-1">
                    <input value="{{ request('search') }}" type="text" id="search" name="search"
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                        placeholder="Cari nomor PO / rekanan ..." />
                    @if (request('search'))
                        <a href="{{ route('bill.view') }}"
                            class="absolute right-0.5 top-0.5 z-10 rounded-md bg-gray-50 p-2 dark:bg-gray-700">
                            <i class="fa-regular fa-circle-xmark"></i>
                            <span class="sr-only">Clear</span>
                        </a>
                    @endif
                </div>
                <button type="submit"
                    class="ms-3 aspect-square w-10 rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="sr-only">Search</span>
                </button>
            </form>
        </div>
        <div class=" overflow-x-auto sm:rounded-md overflow-y-auto">
            <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="w-16 px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nomor PO</th>
                        <th scope="col" class="px-6 py-3">Rekanan</th>
                        <th scope="col" class="px-6 py-3">Total Tagihan</th>
                        <th scope="col" class="px-6 py-3">Tanggal PO</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="w-16 px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="min-w-[8em] px-6 py-4">{{ $order->po_number }}</td>
                            <td class="min-w-[8em] px-6 py-4">{{ $order->partner->name }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ count($order->bills) }}</td>
                            <td class="min-w-[12em] px-6 py-4">{{ $order->po_date }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4">
                                    <button id="bill-dropdown-{{ $order->id }}"
                                        data-dropdown-toggle="bill-dropdown-dot-{{ $order->id }}"
                                        data-dropdown-placement="left"
                                        class="font-semibold text-gray-400 hover:text-gray-600 hover:dark:text-white"
                                        type="button">
                                        <i class="fa-solid fa-ellipsis text-base"></i>
                                    </button>

                                    <div id="bill-dropdown-dot-{{ $order->id }}"
                                        class="z-40 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-64 border dark:border-gray-600 border-gray-100 dark:bg-gray-700 dark:divide-gray-600">
                                        <ul class="py-2 text-gray-700 dark:text-gray-200 text-xs max-h-64 overflow-y-auto"
                                            aria-labelledby="bill-dropdown-{{ $order->id }}">
                                            @forelse ($order->bills as $bill)
                                                <li>
                                                    <div class="flex">
                                                        <a href="/bill/{{ $bill->id }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-balance">
                                                            {{ $bill->bap }}
                                                        </a>
                                                        @can('bill_delete')
                                                            <button
                                                                class="btn-delete-bill px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                                                data-bill-id="{{ $bill->id }}">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </li>
                                            @empty
                                                <li>
                                                    <div
                                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                        Tidak ada tagihan
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr
                            class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                            <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="6">
                                Tidak ada data item tagihan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($orders->count() > 0)
                {{ $order->links }}
            @endif

            @can('bill_delete')
                <form method="post" id="bill-delete-form">
                    @method('DELETE')
                    @csrf
                </form>
            @endcan
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            function priceFormatter(price) {
                price = price.replace(/[^0-9]/g, '');
                return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            $('.price').each(function() {
                const price = priceFormatter($(this).text())
                $(this).text(`Rp ${price}`)
            });
        });
    </script>
    @can('bill_delete')
        <script>
            $(function() {
                $('.btn-delete-bill').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan menghapus tagihan terkait?',
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
                            const billId = $(this).data('bill-id');
                            const form = $('#bill-delete-form');
                            form.attr('action', `/bill/${billId}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endcan
@endpush
