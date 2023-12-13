<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tst_name' => 'required|string|max:255',
            'tst_phone' => 'required|string|max:255',
            'tst_address' => 'required|string|max:255',
            'tst_email' => 'required|string|max:255',
            'tst_note' => 'nullable|string|max:255',
        ];
    }
}
