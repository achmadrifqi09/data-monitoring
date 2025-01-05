@extends('layouts.app')

@section('title', 'Tambah BPL')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Tambah Item BPL</h4>
        <button
            class="bg-red-100 mt-6 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-white"
            type="button" id="add-field-row">
            Tambah Form
        </button>
        <form action="/bpl/{{ $bpl_number }}/items" method="POST">
            @csrf
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6 p-4 border border-dashed rounded-lg dark:border-gray-600 border-gray-300">
                <x-input-label id="item_name-0" label="Nama Item *" name="items[0][item_name]" placeholder="Masukkan nama item"
                    required :isSpaceY="false" />
                <x-input-label id="unit-0" label="Satuan" name="items[0][unit]" placeholder="Masukkan unit"
                    :isSpaceY="false" />
                <x-input-label id="brand-0" label="Merk" name="items[0][brand]" placeholder="Masukkan merk"
                    :isSpaceY="false" />
                <x-input-label id="specification-0" label="Spesifikasi" name="items[0][specification]"
                    placeholder="Masukkan spesifikasi" :isSpaceY="false" />
            </div>
            <div id="field-container"></div>
            <div class="mt-6 flex justify-end">
                <x-button class="min-w-40" type="submit">Submit</x-button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let counter = 1;
            $('#add-field-row').on('click', function() {
                const newRow = `
                    <div
                        id="${counter}"
                        class="relative grid grid-cols-1 md:grid-cols-3 gap-4 my-6 p-4 border border-dashed rounded-lg dark:border-gray-600 border-gray-300">
                        <x-input-label id="item_name-${counter}" label="Nama Item *" name="items[${counter}][item_name]" placeholder="Masukkan nama item"
                            required :isSpaceY="false" />
                        <x-input-label id="unit-${counter}" label="Satuan" name="items[${counter}][unit]" placeholder="Masukkan unit"
                            :isSpaceY="false" />
                        <x-input-label id="brand-${counter}" label="Merk" name="items[${counter}][brand]" placeholder="Masukkan merk"
                            :isSpaceY="false" />
                        <x-input-label id="specification-${counter}" label="Spesifikasi" name="items[${counter}][specification]"
                            placeholder="Masukkan spesifikasi" :isSpaceY="false" />
                        <button data-field-row-id="${counter}" type="button" class="remove-field-row absolute -top-2.5 -right-2.5 text-xl dark:bg-clay bg-gray-50 rounded-full">
                            <i class="fa-regular fa-circle-xmark block"></i>
                        </button>
                    </div>
                `;
                $('#field-container').append(newRow);
                counter++;

                $('.remove-field-row').on('click', function() {
                    const rowId = $(this).data('field-row-id')
                    $(`#${rowId}`).remove()
                })
            });

        })
    </script>
@endpush
