<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AISearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Bisa diatur true untuk semua atau gunakan middleware auth
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
            'query' => 'required|string|min:3|max:500',
            'filters' => 'sometimes|array',
            'filters.category' => 'sometimes|string',
            'filters.year' => 'sometimes|integer|min:2000|max:' . (date('Y') + 1),
            'filters.type' => 'sometimes|string|in:perda,perwal,kepwal,surat_edaran',
            'limit' => 'sometimes|integer|min:1|max:50',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Pertanyaan pencarian tidak boleh kosong',
            'query.min' => 'Pertanyaan minimal 3 karakter',
            'query.max' => 'Pertanyaan maksimal 500 karakter',
            'filters.year.integer' => 'Tahun harus berupa angka',
            'limit.max' => 'Maksimal 50 hasil per pencarian',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Trim query string
        if ($this->has('query')) {
            $this->merge([
                'query' => trim($this->input('query'))
            ]);
        }

        // Set default limit if not provided
        if (!$this->has('limit')) {
            $this->merge([
                'limit' => 10
            ]);
        }
    }
}