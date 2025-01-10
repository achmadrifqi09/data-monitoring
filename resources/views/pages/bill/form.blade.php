@extends('layouts.app')

@section('title', 'Form Tambah Tagihan')

@section('content')
    <div>
        <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h4 class="text-xl font-medium">Formulir Tagihan</h4>
                <p class="text-sm dark:text-gray-300 xl:text-base italic mt-1">
                    *Jika jumlah item ditagih kosong maka data item tersebut tidak akan terecord pada sistem
                </p>
            </div>
        </div>
        <div>
            <div class="w-full text-mirage">
                <label for="order_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Pilih Data Order *
                </label>
                <div class="w-full">
                    <select class=order_id" id="order_id" name="order_id" required></select>
                    @error('order_id')
                        <p class="mt-2 text-sm text-red-600 dark:dark:text-gray-400 hover:dark:text-white">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
            <form action="/bill" method="POST" id="bill-form">
                @method('POST')
                @csrf
                <div class="p-4 border border-dashed border-gray-300 dark:border-gray-600 mt-8 rounded-md">
                    <h5 class="font-medium">Formulir Tagihan Item</h5>
                    <div id="bill-field-container">
                        <div id="bill-field-no-content">
                            <p class="text-gray-400 text-sm py-3 bg-gray-50 dark:bg-gray-700 px-2 mt-4 rounded text-center dark:text-gray-300"
                                id="alert-message">
                                Pilih data order untuk mendapatkan item tagihan
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border border-dashed border-gray-300 dark:border-gray-600 mt-8 rounded-md p-4">
                    <h4 class="text-md font-medium">Formulir Tagihan</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 mt-2">
                        <input type="number" name="order_id" id="order-id" hidden required>
                        <x-input-label id="raw_bill_total" name="raw_bill_total" label="Tagihan*" type="number"
                            :isSpaceY="false" required />
                        <x-input-label id="fee_deduction" name="fee_deduction" label="Potongan*" type="number"
                            :isSpaceY="false" required />
                        <x-input-label id="bap" name="bap" label="BAP*" :isSpaceY="false" required />
                        <x-input-label id="bap" name="date_of_bap" type="date" label="Tanggal BAP*"
                            :isSpaceY="false" required />
                        <x-input-label id="retention" name="retention" label="Rentensi*" :isSpaceY="false" required />
                        <x-input-label id="ppn" name="ppn" step="0.000000000001" type="number" label="PPN*"
                            :isSpaceY="false" required />
                        <x-input-label id="pph" name="pph" step="0.000000000001" type="number" label="PPH*"
                            :isSpaceY="false" required />
                        <x-input-label id="receipt_date" name="receipt_date" type="date" label="Tgl Kuitansi*"
                            :isSpaceY="false" required />
                        <x-input-label id="due_date" name="due_date" type="number" label="Jatuh Tempo*" :isSpaceY="false"
                            required />

                    </div>
                </div>
                @if ($errors->any())
                    <div class="text-red-700 p-4 rounded mb-4 mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mt-6 flex w-full justify-end">
                    <x-button type="submit">
                        Submit
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const orderSelect = $('#order_id');
            orderSelect.on("change", async function(e) {
                const orderId = e.target.value;
                $('#order-id').val(orderId);
                const itemReceiveds = await getItemReceived(orderId);
                generateBillItemField(itemReceiveds, orderId);
            });

            function generateBillItemField(data, orderId) {
                const noFieldElement = $('#bill-field-no-content')
                const alertComponent = $('#alert-message')
                let totalBill = 0;

                if (Array.isArray(data)) {
                    const container = $('#bill-field-container');
                    if (data?.length > 0) {
                        if (noFieldElement) noFieldElement.hide()
                        container.empty()
                        data.forEach((item, index) => {
                            const fields = `
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6" id="bill-fileds-container-${index}">
                                <input type="number" name="bill_items[${index}][item_id]" value="${item.item_id}" hidden>
                                <input type="number" name="bill_items[${index}][price]" value="${item.price}" hidden class="price">
                                <x-input-label id="item_name_${index}" name="bill_items[${index}][item_name]" value="${item.item_name}" label="Nama Item" disabled
                                    class="disabled:cursor-not-allowed" />
                                <x-input-label id="total_amount_received_${index}" name="bill_items[${index}][total_amount_received]" label="Item Diterima" disabled
                                    class="disabled:cursor-not-allowed" value="${item.total_amount_received}"/>
                               <div class"relative">
                                    <x-input-label id="total_item_billed_${index}" name="bill_items[${index}][total_item_billed]" label="Jumlah item ditagih" type="number"
                                        step="0.000000000001" min="0" max="${item.volume}" class="total_item_billed" data-field-container-id="bill-fileds-container-${index}"/>
                                </div>
                            </div>
                            `;
                            container.append(fields);
                        })
                        container.append(
                            '<h6 class="text-base w-full text-end" id="total-bill">Total Tagihan Rp. 0</h6>');

                        function recalculateTotal() {
                            let newTotal = 0;
                            $('.total_item_billed').each(function() {
                                const containerFieldId = $(this).data('field-container-id');
                                const containerField = $(`#${containerFieldId}`);
                                const price = Number(containerField.find('.price').val());
                                const quantity = parseFloat($(this).val()) || 0;
                                newTotal += price * quantity;
                            });

                            totalBill = newTotal; // Update total keseluruhan
                            $('#total-bill').text(`Total Tagihan Rp. ${priceFormatter(totalBill.toString())}`);
                            $('#raw_bill_total').val(totalBill.toString());
                        }

                        $('.total_item_billed').on('change', function() {
                            recalculateTotal();
                        })
                    } else {
                        alertComponent.text('Tidak ada item yang perlu dibayar')
                        $('#bill-field-no-content').show()
                    }
                }
            }

            function priceFormatter(price) {
                price = price.replace(/[^0-9]/g, '');
                return price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            async function getItemReceived(orderId) {
                try {
                    return await $.ajax({
                        url: `/api/order/${orderId}/item-received`,
                        type: 'GET',
                        dataType: 'json'
                    });
                } catch (error) {
                    console.error('Error fetching items:', error);
                    return null;
                }
            }

            $('#order_id').select2({
                ajax: {
                    url: '{{ route('order.api.get') }}',
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
                                text: `${order.po_number} - ${order.partner.name}`
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
@endpush
