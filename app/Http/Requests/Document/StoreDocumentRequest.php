<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf|max:10240',
            'group_id' => 'nullable|exists:groups,id',
        ];
    }

    /**
     * Membuat Validasi Buatan
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul dokumen tidak boleh kosong.',
            'title.string'   => 'Judul harus berupa teks.',
            'title.max'      => 'Judul tidak boleh lebih dari :max karakter.',

            'document.required' => 'Anda harus memilih sebuah file dokumen.',
            'document.file'     => 'Input harus berupa file.',
            'document.mimes'    => 'Dokumen harus berformat :values.',
            'document.max'      => 'Ukuran dokumen tidak boleh lebih dari :max kilobyte.',

            'group_id.exists' => 'Grup yang dipilih tidak valid.',
        ];
    }
}