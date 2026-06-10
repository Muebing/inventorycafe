<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
        ];
    }
}
