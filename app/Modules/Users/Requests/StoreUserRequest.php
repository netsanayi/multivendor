<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('users.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|exists:roles,name',
            'default_currency_id' => 'nullable|exists:currencies,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'email_verified' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'last_name.required' => 'Soyad alanı zorunludur.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.confirmed' => 'Şifre onayı eşleşmiyor.',
            'role.required' => 'Rol seçimi zorunludur.',
            'role.exists' => 'Seçilen rol bulunamadı.',
            'profile_photo.image' => 'Profil fotoğrafı bir resim dosyası olmalıdır.',
            'profile_photo.max' => 'Profil fotoğrafı maksimum 2MB olmalıdır.',
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
            'email_verified' => $this->email_verified ? true : false,
        ]);

        // Set name field
        if ($this->first_name && $this->last_name) {
            $this->merge([
                'name' => $this->first_name . ' ' . $this->last_name
            ]);
        }
    }
}
