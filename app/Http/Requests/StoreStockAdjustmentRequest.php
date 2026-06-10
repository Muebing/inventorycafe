<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'required|exists:items,id',
            'jumlah_penyesuaian' => 'required|integer',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ];
    }
}
