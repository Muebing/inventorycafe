<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_item' => 'required|string|max:100',
            'kategori_id' => 'required|exists:categories,id',
            'satuan' => 'required|string|in:kg,pcs,liter',
            'stok_minimum' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'satuan.in' => 'Satuan harus kg, pcs, atau liter.',
        ];
    }
}
