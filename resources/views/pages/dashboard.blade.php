@extends('layouts.app')

@section('title', 'Dashbard')

@section('content')
    <div class="pt-4">
        <div id="countdown-banner" tabindex="-1"
            class="sticky top-12 z-40 flex flex-1 flex-col md:flex-row justify-between p-4 bg-white border border-gray-200 rounded-lg dark:bg-clay dark:border-gray-700 mb-4">
            <div class="flex flex-col items-start mb-3 me-4 md:items-center md:flex-row md:mb-0 w-full">
                <div
                    class="w-8 h-8 rounded-full bg-red-600 mr-4 flex justify-center items-center ring-[6px] dark:ring-red-800 ring-red-400 ">
                    <i class="fa-solid fa-hourglass-start text-md text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 -mb-0.5">Waktu Tersisa</p>
                    <p id="countdown"></p>
                </div>
            </div>
            <div class="flex items-center flex-shrink-0">
                <button data-dismiss-target="#countdown-banner" type="button"
                    class="flex-shrink-0 inline-flex justify-center w-7 h-7 items-center text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close banner</span>
                </button>
            </div>
        </div>
        <h4 class="mt-4 text-xl font-medium mb-4">Dashboard</h4>
        <div class="flex gap-4 flex-wrap">
            <div
                class="bg-white border border-gray-200 dark:border-gray-700 dark:bg-clay py-4 px-6 rounded-lg w-full sm:w-max flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-full bg-red-600 flex justify-center items-center ring-[6px] dark:ring-red-800 ring-red-400">
                    <i class="fa-solid fa-helmet-safety text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Proyek</p>
                    <p class="font-medium">{{ $project->name }}</p>
                </div>
            </div>
            <div
                class="bg-white border border-gray-200 dark:border-gray-700 dark:bg-clay py-4 px-6 rounded-lg w-full sm:w-max flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-full bg-red-600 flex justify-center items-center ring-[6px] dark:ring-red-800 ring-red-400">
                    <i class="fa-regular fa-calendar text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Direncanakan</p>
                    <p class="font-medium">{{ $project->planned_date }}</p>
                </div>
            </div>
            <div
                class="bg-white border border-gray-200 dark:border-gray-700 dark:bg-clay py-4 px-6 rounded-lg w-full sm:w-max flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-full bg-red-600 flex justify-center items-center ring-[6px] dark:ring-red-800 ring-red-400">
                    <i class="fa-regular fa-calendar-check text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</p>
                    <p class="font-medium">{{ $project->planned_date }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#countdown').text("Loading ...");
            const project = @json($project);
            if (project.planned_finish) {
                const deadline = new Date(project.planned_finish).getTime();

                const x = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = deadline - now;
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    $('#countdown').text(days + " Hari " + hours + " Jam " + minutes + " Menit " + seconds +
                        " Detik");

                    if (distance < 0) {
                        clearInterval(x);
                        $('#countdown').text("Waktu Habis!");
                    }
                }, 1000);
            } else {
                $('#countdown').text("Gagal mendapatkan timeline project");
            }
        })
    </script>
@endpush
