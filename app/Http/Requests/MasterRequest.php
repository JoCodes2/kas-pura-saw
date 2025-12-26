<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MasterRequest extends FormRequest
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
            'nama_kas' => 'required|string|max:255',
            'saldo'    => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kas.required' => 'Nama kas wajib diisi.',
            'nama_kas.string'   => 'Nama kas harus berupa teks.',
            'nama_kas.max'      => 'Nama kas maksimal 255 karakter.',

            'saldo.numeric' => 'Saldo harus berupa angka.',
            'saldo.min'     => 'Saldo tidak boleh kurang dari 0.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code'    => 422,
                'status'  => 'validation_failed',
                'message' => 'Check your input data',
                'data'    => $validator->errors(),
            ], 422)
        );
    }
}
