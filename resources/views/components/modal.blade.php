@props([
    'title' => 'Modal',
    'id',
])
<div id="{{$id}}" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="fixed bg-black opacity-30 w-screen h-dvh"></div>
    <div class="relative p-4 w-full max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-clay sm:max-w-screen-sm mx-auto">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 mx-auto">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{$title}}
                </h3>
                <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="{{$id}}">
                    x
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 h-max">
                @if(isset($content))
                    {{$content}}
                @endif
            </div>
        </div>
    </div>
</div>
