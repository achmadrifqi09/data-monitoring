@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Detail Pembayaran</h4>
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-none dark:bg-clay">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nomor PO :</p>
                    <p class="text-sm font-medium">{{ $payment->order->po_number }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Nomor BAP :</p>
                    <p class="text-sm font-medium">{{ $payment->bill->bap ?? '-' }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Netto</p>
                    <p class="text-sm font-medium price">{{ $payment->bill->netto }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                    <p class="text-sm font-medium price">{{ $payment->payment_total }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Hutang</p>
                    <p class="text-sm font-medium price">{{ $payment->loan_total }}</p>
                </div>
                <div>
                    <p class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">Presentase Pembayaran</p>
                    <p class="text-sm font-medium">{{ round($payment->payment_percentage, 1) }}%</p>
                </div>
            </div>
            @can('payment_delete')
                <form id="delete-payment-installment" method="POST">
                    @method('delete')
                    @csrf
                </form>
            @endcan
        </div>
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h4 class="font-semibold">Riwayat Pembayaran</h4>
                @can('payment_create')
                    <button data-modal-target="add-payment" data-modal-toggle="add-payment" @disabled(floatval($payment->load_total) === 0)
                        class="me-2 rounded bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-white">
                        <i class="fa-solid fa-plus mr-1.5"></i>
                        Bayar
                    </button>
                @endcan
            </div>
            <div class="relative mt-4 overflow-x-auto sm:rounded-md">
                <table class="w-full text-left text-sm text-mirage dark:text-white rtl:text-right">
                    <thead class="bg-gray-100 text-xs uppercase text-mirage dark:bg-gray-700 dark:text-white">
                        <tr>
                            <th scope="col" class="w-24 px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Tanggal Bayar</th>
                            <th scope="col" class="px-6 py-3">Nominal Bayar</th>
                            <th scope="col" class="px-6 py-3">Bukti Bayar</th>
                            @if (auth()->user()->can('payment_delete') || auth()->user()->can('payment_update'))
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payment->payment_installments as $payment_installment)
                            <tr class="bg-white hover:bg-gray-50 dark:bg-clay dark:hover:bg-clay/80">
                                <td class="w-24 px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="min-w-[12em] px-6 py-4">{{ $payment_installment->payment_date }}</td>
                                <td class="min-w-[12em] px-6 py-4 price">
                                    {{ $payment_installment->nominal_payment ?? '-' }}
                                </td>
                                <td class="min-w-[12em] px-6 py-4">
                                    @if ($payment_installment->payment_proof)
                                        <a href="/document/payment/proof?path={{ $payment_installment->payment_proof }}"
                                            target="_blank"
                                            class="font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white">
                                            <i class="fa-solid fa-up-right-from-square text-base"></i>
                                        </a>
                                    @else
                                        <button data-payment-installment-id="{{ $payment_installment->id }}"
                                            class="btn-payment-proof rounded bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-white">
                                            Upload
                                        </button>
                                    @endif
                                </td>

                                @if (auth()->user()->can('payment_delete') || auth()->user()->can('payment_update'))
                                    <td class="px-6 py-4">
                                        <div class="flex gap-4">
                                            @can('payment_update')
                                                <button
                                                    class="btn-payment-installment-update font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                                    data-modal-target="update-payment-installment-modal"
                                                    data-modal-toggle="update-payment-installment-modal"
                                                    data-payment-installment="{{ json_encode($payment_installment) }}">
                                                    <i class="fa-solid fa-pen-to-square text-base"></i>
                                                </button>
                                            @endcan
                                            @can('payment_delete')
                                                <button
                                                    class="delete-payment-installment font-semibold text-gray-500 dark:text-gray-400 hover:dark:text-white"
                                                    data-payment-installment-id="{{ $payment_installment->id }}">
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
                                <td class="bg-white px-6 py-4 text-center dark:bg-clay"
                                    colspan="{{ auth()->user()->can('bpl_delete') || auth()->user()->can('payment_update') ? 7 : 6 }}">
                                    Tidak ada data item/barang
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @can('payment_create')
            <x-modal id="add-payment" title="Pembayaran">
                <x-slot name="content">
                    <form action="/payment/installment" method="POST" class="w-full" enctype="multipart/form-data">
                        @csrf
                        <input type="number" name="payment_id" value="{{ $payment->id }}" hidden>
                        <x-input-label id="payment_total" name="payment_total" label="Jumlah Bayar *" type="number" required />
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
        @can('payment_update')
            <x-modal id="upload-payment-proof-modal" title="Upload Bukti Pembayaran">
                <x-slot name="content">
                    <form method="POST" class="w-full" enctype="multipart/form-data" id="upload-payment-proof">
                        @method('PATCH')
                        @csrf
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
            <x-modal id="update-payment-installment-modal" title="Pembayaran">
                <x-slot name="content">
                    <form id="update-payment-installment" method="POST" class="w-full" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        <input type="number" name="payment_id" value="{{ $payment->id }}" hidden>
                        <x-input-label id="payment_total_update" name="payment_total" label="Jumlah Bayar *" type="number"
                            required />
                        <x-input-label id="payment_date_update" name="payment_date" label="Tanggal Bayar *" type="date"
                            required />
                        <div class="relative">
                            <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Bukti Bayar (Gambar)
                            </label>
                            <input id="payment_proof_update" type="file" name="payment_proof" accept="image/*"
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

    @can('payment_create')
        <script>
            $(function() {
                const paymentProofModal = document.querySelector('#upload-payment-proof-modal')
                const paymentProofUploadModal = new Modal(paymentProofModal, {}, {
                    id: 'upload-payment-proof-modal',
                    override: true
                })

                $('.btn-payment-proof').on('click', function() {
                    const paymentInstallmentId = $(this).data('payment-installment-id')
                    if (paymentInstallmentId) {
                        $('#upload-payment-proof').attr('action',
                            `/payment/{{ $payment->id }}/installment/${paymentInstallmentId}/payment-proof`
                        )
                        paymentProofUploadModal.show()
                    }

                })
            })
        </script>
    @endcan

    @can('payment_update')
        <script>
            $(function() {
                $('.btn-payment-installment-update').on('click', function() {
                    const paymentInstallment = $(this).data('payment-installment')
                    $('#payment_total_update').val(paymentInstallment.nominal_payment)
                    $('#payment_date_update').val(paymentInstallment.payment_date)
                    $('#update-payment-installment').attr(
                        'action',
                        `/payment/{{ $payment->id }}/installment/${paymentInstallment.id}`
                    )
                })
            })
        </script>
    @endcan

    @can('payment_delete')
        <script>
            $('.delete-payment-installment').on('click', function() {
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
                        const paymentInstallmentId = $(this).data('payment-installment-id');
                        const form = $('#delete-payment-installment');
                        form.attr('action',
                            `/payment/{{ $payment->id }}/installment/${paymentInstallmentId}`);
                        form.trigger('submit');
                        form.preventDefault();
                    }
                });
            });
        </script>
    @endcan
@endpush
