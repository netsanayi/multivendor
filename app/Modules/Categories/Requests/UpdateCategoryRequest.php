<?php

namespace App\Modules\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('categories.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $category = $this->route('category');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category->id]), // Cannot be its own parent
                function ($attribute, $value, $fail) use ($category) {
                    // Check if the parent is not a descendant of this category
                    if ($this->isDescendant($category, $value)) {
                        $fail('Seçilen üst kategori, bu kategorinin alt kategorisi olamaz.');
                    }
                },
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_in_footer' => 'boolean',
            'status' => 'sometimes|required|boolean',
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
            'parent_id.not_in' => 'Kategori kendi üst kategorisi olamaz.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.max' => 'Resim boyutu maksimum 2MB olmalıdır.',
            'status.required' => 'Durum alanı zorunludur.',
        ];
    }

    /**
     * Check if a category is a descendant of another
     */
    protected function isDescendant($category, $potentialParentId)
    {
        $descendants = $this->getAllDescendants($category);
        return in_array($potentialParentId, $descendants);
    }

    /**
     * Get all descendant IDs of a category
     */
    protected function getAllDescendants($category)
    {
        $descendants = [];
        $children = \App\Modules\Categories\Models\Category::where('parent_id', $category->id)->get();
        
        foreach ($children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $this->getAllDescendants($child));
        }
        
        return $descendants;
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
            'show_in_menu' => $this->show_in_menu ? true : false,
            'show_in_footer' => $this->show_in_footer ? true : false,
            'status' => $this->status ? true : false,
        ]);
    }
}
