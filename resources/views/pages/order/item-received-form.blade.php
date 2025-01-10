@extends('layouts.app')

@section('title', 'Penerimaan Barang')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Formilir Penerimaan Barang</h4>
        <p class="text-sm dark:text-gray-300">Penerimaan barang dari order {{ $po_number }}</p>
        <p class="mt-2 text-sm italic dark:text-gray-300">
            *Jika field jumlah diterima kosong, otomatis data tidak ditambahkan ke daftar barang diterima
        </p>
        <form action="/item-received" method="POST">
            @csrf
            <input type="number" name="order_id" value="{{ $order_id }}" hidden />
            @foreach ($BPLs as $bpl)
                <div class="mt-6 rounded-lg border border-dashed border-gray-200 p-4 dark:border-gray-700">
                    <p class="font-medium">BPL Ke-{{ $loop->iteration }}</p>
                    <x-input-label id="bpl-{{ $loop->iteration - 1 }}-display" label="Nomor BPL"
                        name="bpl[{{ $loop->iteration - 1 }}][bpl_number]" value="{{ $bpl->bpl_number }}" disabled
                        class="disabled:opacity-80 disabled:hover:cursor-not-allowed" />
                    <input type="text" hidden name="bpl[{{ $loop->iteration - 1 }}][bpl_number]"
                        value="{{ $bpl->bpl_number }}" />
                    <hr class="mt-6 border-gray-200 dark:border-gray-700" />
                    @foreach ($bpl->items as $item)
                        <div class="my-6">
                            <div class="sm:grid-flow-col-2 grid grid-cols-1 gap-4 lg:grid-cols-3">
                                <input type="number" value="{{ $item->id }}"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][item_id]"
                                    hidden />
                                <input type="number" value="{{ $item->order_item->price }}"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][price]"
                                    hidden />
                                <x-input-label
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][item_name]"
                                    id="item_name_{{ $loop->iteration - 1 }}" label="Nama Item" :isSpaceY="false"
                                    value="Item {{ $item->id }} / {{ $item->item_name }}" disabled
                                    class="disabled:opacity-80 disabled:hover:cursor-not-allowed" />

                                <div class="relative">
                                    <x-input-label type="number"
                                        name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][amount_received]"
                                        id="amount_received_{{ $loop->iteration - 1 }}" min="0" step="0.01"
                                        max="{{ $item->order_item->volume }}" label="Volume Diterima" :isSpaceY="false" />

                                    <div class="flex py-1.5 px-1 absolute right-2 top-8 bg-gray-700">
                                        <span>
                                            @php
                                                $itemReceivedTotal = 0;
                                            @endphp
                                            @foreach ($item->item_receiveds as $itemReceived)
                                                @php
                                                    $itemReceivedTotal += $itemReceived->amount_received;
                                                @endphp
                                            @endforeach
                                            {{ $itemReceivedTotal }}
                                        </span>
                                        <span>/{{ $item->order_item->volume }}</span>
                                    </div>
                                </div>

                                <x-input-label type="date"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][received_date]"
                                    id="received_date_{{ $loop->iteration - 1 }}" label="Tanggal Diterima"
                                    :isSpaceY="false" class="date-picker" />
                            </div>
                            <hr class="mt-6 border-gray-200 dark:border-gray-700" />
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="mt-6 flex justify-end">
                <x-button class="min-w-40" type="submit">Submit</x-button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const today = new Date().toISOString().split('T')[0];

            $('.date-picker').each(function() {
                $(this).val(today);
            });
        });
    </script>
@endpush
