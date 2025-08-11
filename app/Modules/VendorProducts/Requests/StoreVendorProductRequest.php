<?php

namespace App\Modules\VendorProducts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole(['vendor', 'admin', 'super-admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'vendor_id' => 'nullable|exists:users,id',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'cost' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1|gte:min_quantity',
            'sku' => 'nullable|string|max:100|unique:vendor_products,sku',
            'barcode' => 'nullable|string|max:100|unique:vendor_products,barcode',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'shipping_class' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'is_virtual' => 'boolean',
            'requires_shipping' => 'boolean',
            'track_inventory' => 'boolean',
            'allow_backorders' => 'boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|in:percentage,fixed',
            'warranty_period' => 'nullable|integer|min:0',
            'warranty_type' => 'nullable|string|max:50',
            'return_period' => 'nullable|integer|min:0',
            'condition' => 'nullable|in:new,used,refurbished',
            'availability' => 'nullable|in:in_stock,out_of_stock,pre_order,discontinued',
            'lead_time' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Ürün seçimi zorunludur.',
            'product_id.exists' => 'Seçilen ürün bulunamadı.',
            'vendor_id.exists' => 'Seçilen satıcı bulunamadı.',
            'price.required' => 'Fiyat zorunludur.',
            'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.',
            'price.min' => 'Fiyat 0\'dan küçük olamaz.',
            'compare_price.numeric' => 'Karşılaştırma fiyatı sayısal bir değer olmalıdır.',
            'compare_price.gt' => 'Karşılaştırma fiyatı, satış fiyatından büyük olmalıdır.',
            'quantity.required' => 'Stok miktarı zorunludur.',
            'quantity.integer' => 'Stok miktarı tam sayı olmalıdır.',
            'quantity.min' => 'Stok miktarı negatif olamaz.',
            'min_quantity.integer' => 'Minimum miktar tam sayı olmalıdır.',
            'min_quantity.min' => 'Minimum miktar 1\'den küçük olamaz.',
            'max_quantity.integer' => 'Maksimum miktar tam sayı olmalıdır.',
            'max_quantity.gte' => 'Maksimum miktar, minimum miktardan büyük veya eşit olmalıdır.',
            'sku.unique' => 'Bu SKU kodu zaten kullanılıyor.',
            'barcode.unique' => 'Bu barkod zaten kullanılıyor.',
            'commission_rate.numeric' => 'Komisyon oranı sayısal bir değer olmalıdır.',
            'commission_rate.min' => 'Komisyon oranı negatif olamaz.',
            'commission_rate.max' => 'Komisyon oranı %100\'den fazla olamaz.',
            'commission_type.in' => 'Geçersiz komisyon tipi.',
            'condition.in' => 'Geçersiz ürün durumu.',
            'availability.in' => 'Geçersiz stok durumu.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Vendor ID yoksa giriş yapan kullanıcıyı ata (vendor ise)
        if (!$this->filled('vendor_id') && auth()->user()->hasRole('vendor')) {
            $this->merge([
                'vendor_id' => auth()->id()
            ]);
        }

        // Varsayılan değerler
        if (!$this->filled('commission_type')) {
            $this->merge(['commission_type' => 'percentage']);
        }

        if (!$this->filled('condition')) {
            $this->merge(['condition' => 'new']);
        }

        if (!$this->filled('availability')) {
            $this->merge(['availability' => $this->quantity > 0 ? 'in_stock' : 'out_of_stock']);
        }

        // Dijital ürünler için shipping gereksiz
        if ($this->is_digital || $this->is_virtual) {
            $this->merge(['requires_shipping' => false]);
        }

        // Track inventory varsayılan olarak true
        if (!$this->filled('track_inventory')) {
            $this->merge(['track_inventory' => true]);
        }
    }
}
