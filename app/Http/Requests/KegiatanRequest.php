<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KegiatanRequest extends FormRequest
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
            'nama_pengaju'     => 'required|string|max:255',
            'no_hp'            => 'required|string|max:20',
            'nama_kegiatan'    => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'estimasi_biaya'   => 'required|numeric|min:0',
            'file_proposal'    => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status_kegiatan'  => 'nullable|in:menunggu,diproses,ditolak,ditunda,diadakan',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pengaju.required' => 'Nama pengaju wajib diisi.',
            'nama_pengaju.string'   => 'Nama pengaju harus berupa teks.',
            'nama_pengaju.max'      => 'Nama pengaju maksimal 255 karakter.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.string'   => 'Nomor HP harus berupa teks.',
            'no_hp.max'      => 'Nomor HP maksimal 20 karakter.',

            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'nama_kegiatan.string'   => 'Nama kegiatan harus berupa teks.',
            'nama_kegiatan.max'      => 'Nama kegiatan maksimal 255 karakter.',

            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal_kegiatan.date'     => 'Format tanggal kegiatan tidak valid.',

            'estimasi_biaya.required' => 'Estimasi biaya wajib diisi.',
            'estimasi_biaya.numeric'  => 'Estimasi biaya harus berupa angka.',
            'estimasi_biaya.min'      => 'Estimasi biaya tidak boleh kurang dari 0.',

            'file_proposal.file'  => 'File proposal harus berupa file.',
            'file_proposal.mimes' => 'File proposal harus berformat PDF, DOC, atau DOCX.',
            'file_proposal.max'   => 'Ukuran file proposal maksimal 2MB.',

            'status_kegiatan.in' => 'Status kegiatan tidak valid.',
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
