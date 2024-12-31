@extends('layouts.app')

@section('title', 'Proyek')

@section('content')
    <div class="mb-8 mt-4">
        <div>
            <h4 class="text-xl font-medium">Proyek</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan timeline proyek</p>
        </div>

        <div class="mt-6 bg-white border border-gray-200 dark:border-none dark:bg-clay p-4 rounded-lg ">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Nama Proyek</p>
                    <p class="text-lg font-medium">{{ $project->name }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal Direncanakan</p>
                    <p class="text-lg font-medium">{{ $project->planned_date }}</p>
                </div>
                <div>
                    <p class="text-sm mb-1.5 text-gray-500 dark:text-gray-400">Tanggal Selesai/Deadline</p>
                    <p class="text-lg font-medium">{{ $project->planned_finish }}</p>
                </div>
            </div>
            <button type="button" data-modal-target="update-project" data-modal-toggle="update-project"
                class="px-3 py-2 mt-6 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                Update
            </button>
        </div>
        <x-modal id="update-project" title="Update Proyek">
            <x-slot name="content">
                <form method="post" id="update-project" action="/control/project/{{ $project->id }}">
                    @method('put')
                    @csrf
                    <x-input-label id="name" name="name" label="Nama Proyek*" placeholder="Nama proyek" />
                    <x-input-label id="planned_date" name="planned_date" label="Tanggal Direncanakan*" type="date"
                        placeholder="Tanggal proyek direncanankan" />
                    <x-input-label id="planned_finish" name="planned_finish" label="Tanggal Selesai*" type="date"
                        placeholder="Tanggal proyek diselesaikan" />
                    <div class="mt-6 flex justify-end">
                        <x-button type="submit">
                            Submit
                        </x-button>
                    </div>
                </form>
            </x-slot>
        </x-modal>
    @endsection

    @push('scripts')
        <script>
            $(function() {
                const project = @json($project);
                $('#name').val(project.name)
                $('#planned_date').val(project.planned_date)
                $('#planned_finish').val(project.planned_finish)
            })
        </script>
    @endpush
