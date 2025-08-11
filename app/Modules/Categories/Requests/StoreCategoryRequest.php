<?php

namespace App\Modules\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('categories.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_in_footer' => 'boolean',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kategori adı zorunludur.',
            'name.max' => 'Kategori adı en fazla 255 karakter olabilir.',
            'slug.unique' => 'Bu slug zaten kullanılıyor.',
            'parent_id.exists' => 'Seçilen üst kategori bulunamadı.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.max' => 'Resim boyutu maksimum 2MB olmalıdır.',
            'status.required' => 'Durum alanı zorunludur.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Generate slug if not provided
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->is_featured ? true : false,
            'show_in_menu' => $this->show_in_menu ? true : false,
            'show_in_footer' => $this->show_in_footer ? true : false,
            'status' => $this->status ? true : false,
        ]);

        // Set default order
        if (!$this->order) {
            $this->merge([
                'order' => 0
            ]);
        }
    }
}
