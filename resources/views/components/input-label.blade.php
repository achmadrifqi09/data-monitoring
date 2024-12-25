@props([
    'disabled' => false,
    'isSpaceY' => true,
    'label' => 'Label',
    'id',
])
<div class="{{ $isSpaceY ? 'my-4' : 'my-0 w-full' }}">
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>
    <input id="{{ $id }}" {{ $disabled ? 'disabled' : '' }} value="{{ old($attributes->get('name')) }}"
        {!! $attributes->merge([
            'type' => 'text',
            'class' =>
                'focus:outline-none bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500',
        ]) !!} />

    @error($attributes->get('name'))
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
            {{ $message }}
        </p>
    @enderror
</div>
