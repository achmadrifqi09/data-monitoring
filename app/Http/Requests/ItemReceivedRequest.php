<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemReceivedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'received_items' => 'required|array|min:1',
            'received_items.*.bpl_id' => 'required|exists:bpl,id',
            'received_items.*.amount_received' => 'required|numeric|min:0',
            'received_items.*.received_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'Order item tidak valid',
            'order_id.exists' => 'Order item tidak ditemukan',
            'received_items.required' => 'Barang yang diterima harus diisi',
            'received_items.min' => 'Minimal satu barang harus diisi',
            'received_items.*.bpl_id.required' => 'ID barang tidak valid',
            'received_items.*.bpl_id.exists' => 'ID barang tidak ditemukan',
            'received_items.*.amount_received.required' => 'Volume barang harus diisi',
            'received_items.*.amount_received.numeric' => 'Volume barang harus berupa angka',
            'received_items.*.amount_received.min' => 'Volume barang tidak boleh negatif',
            'received_items.*.received_date.required' => 'Tanggal diterima harus diisi',
            'received_items.*.received_date.date' => 'Format tanggal tidak valid',
        ];
    }
}
