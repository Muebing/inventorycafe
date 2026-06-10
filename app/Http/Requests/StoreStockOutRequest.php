<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'required|exists:items,id',
            'jumlah' => 'required|integer|min:1',
            'tujuan' => 'required|string|max:50',
            'tanggal' => 'required|date',
        ];
    }
}
