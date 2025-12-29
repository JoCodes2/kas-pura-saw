<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KriteriaRequest extends FormRequest
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
            'nama_kriteria' => 'required|string|max:255',
            'tipe'          => 'required|in:benefit,cost',
            'bobot'         => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'nama_kriteria.string'   => 'Nama kriteria harus berupa teks.',
            'nama_kriteria.max'      => 'Nama kriteria maksimal 255 karakter.',

            'tipe.required' => 'Tipe kriteria wajib dipilih.',
            'tipe.in'       => 'Tipe kriteria harus bernilai benefit atau cost.',

            'bobot.required' => 'Bobot kriteria wajib diisi.',
            'bobot.numeric'  => 'Bobot kriteria harus berupa angka.',
            'bobot.min'      => 'Bobot kriteria tidak boleh kurang dari 0.',
            'bobot.max'      => 'Bobot kriteria tidak boleh lebih dari 100.',
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
