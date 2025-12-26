<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KasmasukRequest extends FormRequest
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
            'kas_id'     => 'required|uuid|exists:master_kas,id',
            'tanggal'    => 'required|date',
            'sumber'     => 'required|string|max:255',
            'jumlah'     => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kas_id.required' => 'Kas wajib dipilih.',
            'kas_id.uuid'     => 'Format kas tidak valid.',
            'kas_id.exists'   => 'Kas tidak ditemukan.',

            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date'     => 'Format tanggal tidak valid.',

            'sumber.required' => 'Sumber kas wajib diisi.',
            'sumber.string'   => 'Sumber kas harus berupa teks.',
            'sumber.max'      => 'Sumber kas maksimal 255 karakter.',

            'jumlah.required' => 'Jumlah kas wajib diisi.',
            'jumlah.numeric'  => 'Jumlah kas harus berupa angka.',
            'jumlah.min'      => 'Jumlah kas minimal 0,01.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
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
