<?php

namespace App\Modules\Brands\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('brands.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $brand = $this->route('brand');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands')->ignore($brand->id),
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'status' => 'sometimes|required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Marka adı zorunludur.',
            'name.max' => 'Marka adı en fazla 255 karakter olabilir.',
            'slug.unique' => 'Bu slug zaten kullanılıyor.',
            'logo.image' => 'Logo bir resim dosyası olmalıdır.',
            'logo.max' => 'Logo boyutu maksimum 2MB olmalıdır.',
            'website.url' => 'Geçerli bir website adresi giriniz.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'status.required' => 'Durum alanı zorunludur.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Generate slug if name changed and slug not provided
        if ($this->has('name') && !$this->slug) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->is_featured ? true : false,
            'status' => $this->status ? true : false,
        ]);
    }
}
