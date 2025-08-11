<?php

namespace App\Modules\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('products.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $product = $this->route('product');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('products')->ignore($product->id),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->ignore($product->id),
            ],
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'attributes' => 'nullable|array',
            'attributes.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|exists:uploads,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'status' => 'sometimes|required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'is_virtual' => 'boolean',
            'tax_class' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ürün adı zorunludur.',
            'sku.required' => 'SKU kodu zorunludur.',
            'sku.unique' => 'Bu SKU kodu zaten kullanılıyor.',
            'barcode.unique' => 'Bu barkod zaten kullanılıyor.',
            'category_id.required' => 'Kategori seçimi zorunludur.',
            'category_id.exists' => 'Seçilen kategori bulunamadı.',
            'brand_id.exists' => 'Seçilen marka bulunamadı.',
            'price.required' => 'Fiyat zorunludur.',
            'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.',
            'price.min' => 'Fiyat 0\'dan küçük olamaz.',
            'quantity.required' => 'Stok miktarı zorunludur.',
            'quantity.integer' => 'Stok miktarı tam sayı olmalıdır.',
            'images.*.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'images.*.max' => 'Resim boyutu maksimum 2MB olmalıdır.',
            'status.required' => 'Durum seçimi zorunludur.',
            'status.in' => 'Geçersiz durum seçimi.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Generate slug if not provided
        if ($this->has('name') && !$this->slug) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->is_featured ? true : false,
            'is_digital' => $this->is_digital ? true : false,
            'is_virtual' => $this->is_virtual ? true : false,
        ]);
    }
}
