@extends('layouts.app')

@section('title', 'Tambah order')

@section('content')
    <div>
        <h4 class="mt-4 text-xl font-medium">Formulir Tambah Order</h4>
        <p class="text-sm dark:text-gray-300 xl:text-base">
            Tanda asteris
            <span class="font-semibold italic">(*)</span>
            pada field wajib diisi
        </p>
        <div class="mt-6">
            <form method="post" action="/order">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <x-input-label id="po_number" label="Nomor PO *" name="po_number" placeholder="Masukkan nomor PO"
                        :isSpaceY="false" value="{{ old('po_number') }}" required />
                    <div class="w-full text-mirage">
                        <label for="partner_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Rekanan *
                        </label>
                        <select class=partner_id" id="partner_id" name="partner_id" required></select>
                        @error('partner_id')
                            <p class="mt-2 text-sm text-red-600 dark:dark:text-gray-400 hover:dark:text-white text-gray-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <x-input-label id="description" label="Uraian" name="description" placeholder="Masukkan uraian"
                        :isSpaceY="false" value="{{ old('description') }}" />
                    <x-input-label id="po_date" type="date" label="Tanggal PO *" name="po_date" :isSpaceY="false"
                        value="{{ old('po_date') }}" required />
                    <x-input-label id="start_date" type="date" label="Start *" name="start_date" :isSpaceY="false"
                        value="{{ old('po_start') }}" required />
                    <x-input-label id="finish_date" type="date" label="Finish *" name="finish_date" :isSpaceY="false"
                        value="{{ old('po_finish') }}" required />
                    <div
                        class="w-full p-4 border rounded-lg border-dashed border-gray-300  dark:border-gray-600 sm:col-span-2">
                        <p class="font-medium text-sm mb-4">Tambah Item(BPL) *</p>
                        <button
                            class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-white"
                            type="button" id="add-row">
                            Tambah Field
                        </button>

                        <div class="mt-4 space-y-12 md:space-y-4" id="item-container">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:pr-8">
                                <div class="w-full text-mirage">
                                    <label for="bpl-1"
                                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        Item(BPL) *
                                    </label>
                                    <select class="items" id="items-0" name="items[0][id]"></select>
                                </div>
                                <x-input-label id="volume-0" type="number" label="Volume *" name="items[0][volume]"
                                    :isSpaceY="false" />
                                <x-input-label id="price" type="number" label="Harga *" name="items[0][price]"
                                    :isSpaceY="false" />

                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <x-button class="min-w-40" type="submit">Submit</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#partner_id').select2({
                ajax: {
                    url: '{{ route('partner.api.get') }}',
                    delay: 350,
                    dataType: 'json',
                    data: function(params) {
                        const query = {
                            search: params.term,
                        }
                        return query;
                    },
                    processResults: function(data) {
                        const result = data?.map((partner) => {
                            return {
                                id: partner.id,
                                text: partner.name
                            }
                        })
                        console.log(result)
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
            $('#items-0').select2({
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
            let counter = 1;
            $('#add-row').on('click', function() {
                const newRow = `
                    <div class="flex flex-col md:flex-row gap-4 relative" id="row-${counter}">
                        <div class="w-full text-mirage">
                            <label for="bpl-${counter}"
                                class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Item(BPL) *
                            </label>
                            <select class="items" id="items-${counter}" name="items[${counter}][id]" required></select>
                        </div>
                        <x-input-label id="volume-${counter}" type="number" label="Volume *" name="items[${counter}][volume]"
                                    :isSpaceY="false" />
                        <x-input-label id="price-${counter}" type="number" label="Harga *" name="items[${counter}][price]" :isSpaceY="false" required/>
                        <button type="button" data-row-id="row-${counter}" class="md:h-[42px] md:mt-[28px] absolute top-0 right-1 md:static remove-row">
                          <i class="fa-regular fa-circle-xmark"></i> 
                        </button>
                    </div>
                `;
                $('#item-container').append(newRow);
                generateOptions(`items-${counter}`)
                counter++;

                $('.remove-row').on('click', function() {
                    const rowId = $(this).data('row-id')
                    $(`#${rowId}`).remove()
                })
            });



            function generateOptions(id) {
                $(`#${id}`).select2({
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
            }
        });
    </script>
@endpush
