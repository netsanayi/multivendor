<?php

namespace App\Modules\Addresses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'type' => 'required|in:home,work,billing,shipping,other',
            'is_default' => 'boolean',
            'is_billing' => 'boolean',
            'is_shipping' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Kullanıcı seçimi zorunludur.',
            'user_id.exists' => 'Seçilen kullanıcı bulunamadı.',
            'title.required' => 'Adres başlığı zorunludur.',
            'first_name.required' => 'Ad zorunludur.',
            'last_name.required' => 'Soyad zorunludur.',
            'phone.required' => 'Telefon numarası zorunludur.',
            'address_line_1.required' => 'Adres satırı zorunludur.',
            'city.required' => 'Şehir zorunludur.',
            'state.required' => 'İl/Eyalet zorunludur.',
            'country.required' => 'Ülke zorunludur.',
            'postal_code.required' => 'Posta kodu zorunludur.',
            'type.required' => 'Adres tipi seçimi zorunludur.',
            'type.in' => 'Geçersiz adres tipi.',
        ];
    }
}
