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
            <form method="post">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <x-input-label
                        id="po_number"
                        label="Nomor PO *"
                        name="po_number"
                        placeholder="Masukkan nomor PO"
                        :isSpaceY="false"
                    />
                    <div class="w-full text-mirage">
                        <label for="partner_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Rekanan *
                        </label>
                        <select
                            class="js-example-basic-multiple"
                            id="partner_id"
                            name="partner_id"
                        ></select>
                    </div>
                    <x-input-label
                        id="description"
                        label="Uraian"
                        name="description"
                        placeholder="Masukkan uraian"
                        :isSpaceY="false"
                    />
                    <x-input-label id="po_date" type="date" label="Tanggal PO *" name="po_date" :isSpaceY="false" />
                    <x-input-label id="start_date" type="date" label="Start *" name="start_date" :isSpaceY="false" />
                    <x-input-label
                        id="finish_date"
                        type="date"
                        label="Finish *"
                        name="finish_date"
                        :isSpaceY="false"
                    />
                </div>
                <div class="mt-6 flex justify-end">
                    <x-button class="min-w-40">Submit</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('.js-example-basic-multiple').select2(
                {
                    ajax: {
                        url: '{{ route('partner.data') }}',
                        delay: 350,
                        dataType: 'json',
                        data: function (params) {
                            const query = {
                                search: params.term,
                            }
                            return query;
                        },
                        processResults: function (data) {
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
                }
            );
        });
    </script>
@endsection
