@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">Pembayaran</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data Pembayaran</p>
        </div>
        @if (auth()->user()->can('payment_create') ||
                auth()->user()->can('payment_export') ||
                auth()->user()->can('payment_import'))
            <div class="inline-flex h-max overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
                role="group">
                @can('payment_create')
                    <button type="button" data-modal-target="create-payment" data-modal-toggle="create-payment"
                        class="bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white">
                        Tambah
                    </button>
                @endcan

            </div>
        @endif
    </div>
    <div>
        <form class="mb-4 flex max-w-sm items-center" action="{{ route('payment.view') }}" method="get">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative flex-1">
                <input value="{{ request('search') }}" type="text" id="search" name="search"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                    placeholder="Cari nomor PO / BAP ..." />
                @if (request('search'))
                    <a href="{{ route('payment.view') }}"
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
    <div class="relative overflow-x-auto sm:rounded-md">
        <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
            <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                <tr>
                    <th scope="col" class="w-16 px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">No PO</th>
                    <th scope="col" class="px-6 py-3">Bayar</th>
                    <th scope="col" class="px-6 py-3">Hutang</th>
                    <th scope="col" class="px-6 py-3">Bayar%</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="w-16 px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="min-w-[8em] px-6 py-4">{{ $payment->order->po_number ?? '-' }}</td>
                        <td class="min-w-[12em] px-6 py-4 price">
                            {{ $payment->payment_total ?? '-' }}
                        </td>
                        <td class="min-w-[12em] px-6 py-4 price">{{ $payment->loan_total ?? '-' }}</td>
                        <td class="min-w-[12em] px-6 py-4">{{ round($payment->payment_percentage, 1) ?? '0' }}%</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-4">
                                <a href="/payment/{{ $payment->id }}"
                                    class="edit-button font-semibold text-gray-400 hover:text-gray-600 hover:dark:text-white">
                                    <i class="fa-solid fa-circle-info text-base"></i>
                                </a>
                                @can('payment_delete')
                                    <button data-payment-id="{{ $payment->id }}"
                                        class="delete-payment font-semibold text-gray-400 hover:text-gray-600 hover:dark:text-white">
                                        <i class="fa-solid fa-trash-can text-base"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-clay dark:hover:bg-clay/80">
                        <td class="bg-white px-6 py-4 text-center dark:bg-clay" colspan="7">Tidak ada data pembayaran
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (!$payments->isEmpty())
            {{ $payments->links() }}
        @endif
        @can('payment_delete')
            <form id="payment-delete" method="POST">
                @method('delete')
                @csrf
            </form>
        @endcan
    </div>
    <div>
        @can('payment_create')
            <x-modal id="create-payment" title="Tambah Pembayaran">
                <x-slot name="content">
                    <form action="/payment" method="POST" class="w-full" enctype="multipart/form-data">
                        @csrf
                        <div class="w-full text-mirage">
                            <label for="order_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Order *
                            </label>
                            <select class=order_id" id="order_id" name="order_id" required></select>
                            @error('order_id')
                                <p class="mt-2 text-sm text-red-600 dark:dark:text-gray-400 hover:dark:text-white">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <x-input-label id="payment_total" name="payment_total" label="Jumlah Bayar *" type="number" required />
                        <span class="block text-xs -mt-2 mb-4" id="total-netto"></span>
                        <x-input-label id="payment_date" name="payment_date" label="Tanggal Bayar *" type="date" required />
                        <div class="relative">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Bukti Bayar (Gambar)
                            </label>
                            <input id="payment_proof" type="file" name="payment_proof" accept="image/*"
                                class="w-full rounded-md border border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-300" />
                        </div>
                        <div class="flex justify-end w-full mt-8">
                            <x-button type="submit">
                                Submit
                            </x-button>
                        </div>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
    </div>
@endsection

@push('scripts')
    @can('payment_create')
        <script>
            $(function() {
                $('#order_id').select2({
                    ajax: {
                        url: '/api/order?total_bill=1',
                        delay: 350,
                        dataType: 'json',
                        data: function(params) {
                            const query = {
                                search: params.term,
                            }
                            return query;
                        },
                        processResults: function(data) {
                            const result = data?.map((order) => {
                                return {
                                    id: order.id,
                                    text: `${order.po_number} - ${ order.po_date}`,
                                    data: {
                                        netto: order.total_netto,
                                    },
                                }
                            })
                            return {
                                results: result
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
                $('#order_id').on('select2:select', function(e) {
                    const data = e.params.data;
                    const netto = data.data.netto;
                    const formattedNetto = (price) => {
                        price = price.replace(/[^0-9]/g, '');
                        return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                    $('#total-netto').text(`Netto Rp ${formattedNetto(netto.toString())}`)
                });


            });
        </script>
    @endcan

    @can('payment_delete')
        <script>
            $(function() {
                $('.delete-payment').on('click', function() {
                    const theme = localStorage.getItem('theme');
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin akan item pembayaran tersebut?',
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
                            const paymentId = $(this).data('payment-id');
                            const form = $('#payment-delete');
                            form.attr('action',
                                `/payment/${paymentId}`);
                            form.trigger('submit');
                            form.preventDefault();
                        }
                    });
                })
            })
        </script>
    @endcan

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
@endpush
