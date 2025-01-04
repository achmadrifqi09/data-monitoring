<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'po_number' => 'required|min:4',
            'partner_id' => 'required',
            'description' => 'nullable',
            'po_date' => 'required|date',
            'start_date' => 'required|date',
            'finish_date' => 'required|date',

            'bpl' => 'required|array|min:1',
            'bpl.*.bpl_number' => 'required|min:1',
            'bpl.*.items' => 'required|array|min:1',

            'bpl.*.items.*.id' => 'required|string',
            'bpl.*.items.*.is_selected' => 'nullable',
            'bpl.*.items.*.volume' => 'nullable',
            'bpl.*.items.*.price' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'po_number.required' => 'Nomor PO harus diisi',
            'po_number.min' => 'Nomor PO minimal 4 karakter',
            'partner_id.required' => 'Rekanan harus diisi',
            'po_date.required' => 'Tanggal PO harus diisi',
            'po_date.date' => 'Tanggal PO harus berupa tanggal',
            'start_date.required' => 'Tanggal start harus diisi',
            'start_date.date' => 'Tanggal start harus berupa tanggal',
            'finish_date.required' => 'Tanggal finis harus diisi',
            'finish_date.date' => 'Tanggal finis harus berupa tanggal',
            'bpl.required' => 'BPL harus diisi',
            'bpl.array' => 'BPL harus berupa array',
            'bpl.min' => 'BPL minimal memiliki 1 item',
            'bpl.*.bpl_number.required' => 'Nomor BPL harus diisi',
            'bpl.*.items.required' => 'Items BPL harus diisi',
            'bpl.*.items.array' => 'Items BPL harus berupa array',
            'bpl.*.items.min' => 'Items BPL minimal memiliki 1 item',
            'bpl.*.items.*.id.required' => 'ID item BPL harus diisi',
            'bpl.*.items.*.id.string' => 'ID item BPL harus berupa string',
        ];
    }
}
