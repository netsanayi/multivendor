<?php

namespace App\Modules\Languages\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('languages.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'code' => 'required|string|size:2|unique:languages,code,' . $this->route('language') . '|lowercase',
            'locale' => 'required|string|max:10|unique:languages,locale,' . $this->route('language'),
            'flag' => 'nullable|string|max:10',
            'direction' => 'required|in:ltr,rtl',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'date_format' => 'nullable|string|max:50',
            'time_format' => 'nullable|string|max:50',
            'datetime_format' => 'nullable|string|max:50',
            'decimal_separator' => 'nullable|string|max:1',
            'thousand_separator' => 'nullable|string|max:1'
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
            'name.required' => 'Dil adı zorunludur.',
            'name.max' => 'Dil adı en fazla 100 karakter olabilir.',
            'native_name.max' => 'Yerel dil adı en fazla 100 karakter olabilir.',
            'code.required' => 'Dil kodu zorunludur.',
            'code.size' => 'Dil kodu 2 karakter olmalıdır (ISO 639-1).',
            'code.unique' => 'Bu dil kodu zaten kullanılıyor.',
            'code.lowercase' => 'Dil kodu küçük harf olmalıdır.',
            'locale.required' => 'Locale kodu zorunludur.',
            'locale.max' => 'Locale kodu en fazla 10 karakter olabilir.',
            'locale.unique' => 'Bu locale kodu zaten kullanılıyor.',
            'direction.required' => 'Metin yönü seçilmelidir.',
            'direction.in' => 'Metin yönü soldan sağa (ltr) veya sağdan sola (rtl) olmalıdır.',
            'display_order.integer' => 'Görüntüleme sırası tam sayı olmalıdır.',
            'display_order.min' => 'Görüntüleme sırası negatif olamaz.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Dil kodunu küçük harfe çevir
        if ($this->filled('code')) {
            $this->merge([
                'code' => strtolower($this->code)
            ]);
        }

        // Eğer varsayılan olarak işaretlenmişse, diğerlerini varsayılan olmaktan çıkar
        if ($this->is_default) {
            \App\Modules\Languages\Models\Language::where('is_default', true)
                ->where('id', '!=', $this->route('language'))
                ->update(['is_default' => false]);
        }
    }
}
