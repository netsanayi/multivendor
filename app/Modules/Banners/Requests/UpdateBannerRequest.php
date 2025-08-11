<?php

namespace App\Modules\Banners\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('banners.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:50',
            'button_color' => 'nullable|string|max:20',
            'position' => 'sometimes|required|in:home_slider,home_top,home_middle,home_bottom,category_top,product_sidebar',
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target' => 'in:_self,_blank',
            'status' => 'sometimes|required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Banner başlığı zorunludur.',
            'title.max' => 'Banner başlığı en fazla 255 karakter olabilir.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.max' => 'Görsel boyutu maksimum 4MB olmalıdır.',
            'mobile_image.image' => 'Mobil görsel bir resim dosyası olmalıdır.',
            'mobile_image.max' => 'Mobil görsel boyutu maksimum 2MB olmalıdır.',
            'link.url' => 'Geçerli bir URL giriniz.',
            'position.required' => 'Banner pozisyonu seçimi zorunludur.',
            'position.in' => 'Geçersiz pozisyon seçimi.',
            'end_date.after_or_equal' => 'Bitiş tarihi başlangıç tarihinden önce olamaz.',
            'status.required' => 'Durum alanı zorunludur.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean
        $this->merge([
            'status' => $this->status ? true : false,
        ]);

        // Set default target if not provided
        if (!$this->target) {
            $this->merge(['target' => '_self']);
        }
    }
}
