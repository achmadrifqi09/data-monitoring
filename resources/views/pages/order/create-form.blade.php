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
                            <p class="mt-2 text-sm text-red-600 dark:dark:text-gray-400 hover:dark:text-white">
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="space-y-2">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div
                        class="w-full p-4 border rounded-lg border-dashed border-gray-300  dark:border-gray-600 sm:col-span-2">
                        <p class="font-medium text-sm mb-4">Tambah BPL *</p>
                        <button
                            class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-white"
                            type="button" id="add-row">
                            Tambah Field BPL
                        </button>

                        <div class="mt-4 space-y-12 md:space-y-4" id="items-container">
                            <div id="bpl-container-0">
                                <div class="w-full text-mirage selected-container">
                                    <label for="bpl-0"
                                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                        BPL Ke-1
                                    </label>
                                    <select class="items" id="bpl-0" name="bpl[0][bpl_number]"
                                        data-container-id="0"></select>
                                </div>
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
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
            const defaultBPLSelect = $('#bpl-0');
            defaultBPLSelect.select2({
                ajax: {
                    url: '/api/bpl?not_used=1',
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
                                id: bpl.bpl_number,
                                text: `${bpl?.bpl_number} ${bpl.description || ''} ${bpl?.date_of_use || ''}`
                            }
                        })
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });

            defaultBPLSelect.on("change", async function(e) {
                const bplNumber = e.target.value;
                const items = await getItemOfBPL(bplNumber);
                const containerId = $(this).data('container-id');
                generateInputField(items, containerId)
            });

            async function getItemOfBPL(bplNumber) {
                try {
                    return await $.ajax({
                        url: `/api/item/${bplNumber}`,
                        type: 'GET',
                        dataType: 'json'
                    });
                } catch (error) {
                    console.error('Error fetching items:', error);
                    return null;
                }
            }

            let counter = 0;

            function generateInputField(data, containerId) {
                if (Array.isArray(data)) {
                    const container = $(`#bpl-container-${containerId}`);
                    container.find('.field-container').remove();

                    data.forEach(function(item, index) {
                        const newInputRow = `
                            <div class="flex flex-col md:items-center md:flex-row gap-4 relative my-6 field-container">
                                <input
                                    id="is-selected-${counter}-${index}"
                                    type="checkbox"
                                    data-field-id="${counter}-${index}"
                                    name="bpl[${counter}][items][${index}][is_selected]"
                                    class="h-4 w-4 md:mt-4 rounded border-gray-300 bg-gray-100 text-red-600 focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-red-600"
                                />
                                <input type="number" name="bpl[${counter}][items][${index}][id]" value="${item.id}" hidden>
                                <x-input-label id="item-name-${counter}-${index}" type="text" label="Nama Item"
                                    name="bpl[${counter}][items][${index}][item_name]" value="${item?.item_name}" class="disabled:cursor-not-allowed"
                                    :isSpaceY="false" disabled/>
                                <x-input-label id="volume-${counter}-${index}" type="number" step="0.000000000001" label="Volume"
                                    name="bpl[${counter}][items][${index}][volume]"
                                    :isSpaceY="false"/>
                                <x-input-label id="price-${counter}-${index}" type="number" label="Harga"
                                    name="bpl[${counter}][items][${index}][price]" :isSpaceY="false"/>
                            </div>
                        `;
                        container.append(newInputRow);
                    });

                    $("input[type='checkbox']").on('change', function() {
                        const fieldIdentifier = $(this).data('field-id');
                        if ($(this).is(":checked")) {
                            $(`#volume-${fieldIdentifier}`).attr('required', true);
                            $(`#price-${fieldIdentifier}`).attr('required', true);
                        } else {
                            $(`#volume-${fieldIdentifier}`).removeAttr('required');
                            $(`#price-${fieldIdentifier}`).removeAttr('required');
                        }
                    });
                }
            }

            $('#add-row').on('click', function() {
                const itemContainer = $('#items-container');
                counter++;
                const elements = `
                    <div id="bpl-container-${counter}" class="pt-8 mt-2 border-t border-t-gray-300 dark:border-t-gray-700">
                        <div class="w-full text-mirage selected-container">
                            <div class="flex justify-between">
                                <label for="bpl-${counter}"
                                    class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    BPL Ke-${counter + 1}
                                </label>
                                <button data-container-id="bpl-container-${counter}" class="remove-field-row dark:text-white text-gray-500 text-xs underline" type="button">
                                    Hapus
                                </button>
                            </div>
                            <select class="items" id="bpl-${counter}" name="bpl[${counter}][bpl_number]" data-container-id="${counter}"></select>
                        </div>
                     </div>
                `;
                itemContainer.append(elements);
                generateOptions(`bpl-${counter}`);

                $('.remove-field-row').on('click', function() {
                    const containerId = $(this).data('container-id');
                    if (counter > 0) counter--;
                    $(`#${containerId}`).remove()
                });

                $(`#bpl-${counter}`).on("change", async function(e) {
                    const bplNumber = e.target.value;
                    const containerId = $(this).data('container-id');
                    const items = await getItemOfBPL(bplNumber);
                    generateInputField(items, containerId)
                })
            });

            function generateOptions(id) {
                $(`#${id}`).select2({
                    ajax: {
                        url: '/api/bpl?not_used=1',
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
                                    id: bpl.bpl_number,
                                    text: `${bpl?.bpl_number} ${bpl.description || ''} ${bpl?.date_of_use || ''}`
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
