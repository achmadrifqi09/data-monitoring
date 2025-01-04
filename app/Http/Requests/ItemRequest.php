<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|min:4',
            'items.*.unit' => 'nullable',
            'items.*.brand' => 'nullable',
            'items.*.specification' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Setidaknya harus ada 1 item',
            'items.*.item_name.required' => 'Nama item harus diisi',
            'items.*.item_name.min' => 'Nama item minimal 4 karakter',
        ];
    }
}
