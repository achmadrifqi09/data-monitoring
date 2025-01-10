<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
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
            'order_id' => 'required|numeric',
            'bap' => 'required',
            'date_of_bap' => 'required|date',
            'raw_bill_total' => 'required|numeric',
            'fee_deduction' => 'required|numeric',
            'retention' => 'required|numeric',
            'ppn' => 'required|numeric',
            'pph' => 'required|numeric',
            'receipt_date' => 'required|date',
            'due_date' => 'required|numeric',
            'bill_items' => 'required|array|min:1',
            'bill_items.*.item_id' => 'required',
            'bill_items.*.price' => 'required',
            'bill_items.*.price' => 'required',
            'bill_items.*.total_item_billed' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'Data order harus dipilih',
            'bap.required' => 'BAP harus dipilih',
            'bap.date' => 'Tanggal BAP harus berformat tanggal',
            'order_id.numeric' => 'Data order harus berupa angka',
            'raw_bill_total.required' => 'Tagihan harus diisi',
            'raw_bill_total.numeric' => 'Tagihan harus berupa angka',
            'fee_deduction.required' => 'Potongan harus diisi',
            'fee_deduction.numeric' => 'Potongan harus berupa angka',
            'retention.required' => 'Retensi harus diisi',
            'retention.numeric' => 'Retensi harus berupa angka',
            'ppn.required' => 'PPN harus diisi',
            'ppn.numeric' => 'PPN harus berupa angka',
            'pph.required' => 'PPH harus diisi',
            'pph.numeric' => 'PPH harus berupa angka',
            'receipt_date.required' => 'Tanggal kuitansi harus diisi',
            'receipt_date.date' => 'Tanggal kuitansi harus berformat tanggal',
            'due_date.required' => 'Jatuh tempo harus diisi',
            'due_date.numeric' => 'Jatuh tempo harus berupa angka',

            'bill_items.required' => 'Tagihan item harus diisi minimal 1 item',
            'bill_items.array' => 'Tagihan item harus diisi minimal 1 item',
            'bill_items.min' => 'Tagihan item harus diisi minimal 1 item',
            'bill_items.*.item_id.required' => 'Tagihan item harus diisi minimal 1',
            'bill_items.*.price.required' => 'Tagihan item harus diisi minimal 1',
            'bill_items.*.price.required' => 'Tagihan item harus diisi minimal 1',
            'bill_items.*.total_item_billed.required' => 'Jumlah item ditagih harus diisi',
        ];
    }
}
