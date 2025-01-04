@extends('layouts.app')

@section('title', 'Penerimaan Barang')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Formilir Penerimaan Barang</h4>
        <p class="text-sm dark:text-gray-300">
            Penerimaan barang dari order {{ $po_number }}
        </p>
        <p class="text-sm dark:text-gray-300 italic mt-2">
            *Jika field jumlah diterima kosong, otomatis data tidak ditambahkan ke daftar barang diterima
        </p>
        <form action="/item-received" method="POST">
            @csrf
            <input type="number" name="order_id" value="{{ $order_id }}" hidden>
            @foreach ($BPLs as $bpl)
                <div class="border border-gray-200 border-dashed dark:border-gray-700 p-4 rounded-lg mt-6">
                    <p class="font-medium">BPL Ke-{{ $loop->iteration }}</p>
                    <x-input-label id="bpl-{{ $loop->iteration - 1 }}-display" label="Nomor BPL"
                        name="bpl[{{ $loop->iteration - 1 }}][bpl_number]" value="{{ $bpl->bpl_number }}" disabled
                        class="disabled:hover:cursor-not-allowed disabled:opacity-80" />
                    <input type="text" hidden name="bpl[{{ $loop->iteration - 1 }}][bpl_number]"
                        value="{{ $bpl->bpl_number }}">
                    <hr class="mt-6 dark:border-gray-700 border-gray-200">

                    @foreach ($bpl->items as $item)
                        <div class="my-6">
                            <div class="grid grid-cols-1 sm:grid-flow-col-2 lg:grid-cols-3 gap-4">
                                <input type="number" value="{{ $item->id }}"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][item_id]"
                                    hidden>
                                <input type="number" value="{{ $item->price }}"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][price]"
                                    hidden>
                                <x-input-label
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][item_name]"
                                    id="item_name_{{ $loop->iteration - 1 }}" label="Nama Item" :isSpaceY="false"
                                    value="Item {{ $item->id }} / {{ $item->item_name }}" disabled
                                    class="disabled:hover:cursor-not-allowed disabled:opacity-80" />
                                <x-input-label type="number"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][amount_received]"
                                    id="amount_received_{{ $loop->iteration - 1 }}" min="0"
                                    max="{{ $item->volume }}" label="Volume Diterima" :isSpaceY="false" />
                                <x-input-label type="date"
                                    name="bpl[{{ $loop->parent->iteration - 1 }}][items][{{ $loop->iteration - 1 }}][received_date]"
                                    id="received_date_{{ $loop->iteration - 1 }}" label="Tanggal Diterima"
                                    :isSpaceY="false" class="date-picker" />
                            </div>
                            <hr class="mt-6 dark:border-gray-700 border-gray-200">
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
            const today = new Date().toISOString().split('T')[0]

            $('.date-picker').each(function() {
                $(this).val(today)
            })
        })
    </script>
@endpush
