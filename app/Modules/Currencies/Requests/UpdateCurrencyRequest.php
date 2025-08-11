<?php

namespace App\Modules\Currencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('currencies.update');
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
            'code' => 'required|string|size:3|unique:currencies,code,' . $this->route('currency') . '|uppercase',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'decimal_places' => 'required|integer|min:0|max:4',
            'thousand_separator' => 'nullable|string|max:1',
            'decimal_separator' => 'required|string|max:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'flag' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0'
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
            'name.required' => 'Para birimi adı zorunludur.',
            'name.max' => 'Para birimi adı en fazla 100 karakter olabilir.',
            'code.required' => 'Para birimi kodu zorunludur.',
            'code.size' => 'Para birimi kodu 3 karakter olmalıdır.',
            'code.unique' => 'Bu para birimi kodu zaten kullanılıyor.',
            'code.uppercase' => 'Para birimi kodu büyük harf olmalıdır.',
            'symbol.required' => 'Para birimi sembolü zorunludur.',
            'symbol.max' => 'Para birimi sembolü en fazla 10 karakter olabilir.',
            'symbol_position.required' => 'Sembol pozisyonu seçilmelidir.',
            'symbol_position.in' => 'Sembol pozisyonu önce veya sonra olmalıdır.',
            'exchange_rate.required' => 'Döviz kuru zorunludur.',
            'exchange_rate.numeric' => 'Döviz kuru sayısal bir değer olmalıdır.',
            'exchange_rate.min' => 'Döviz kuru 0\'dan büyük olmalıdır.',
            'decimal_places.required' => 'Ondalık basamak sayısı zorunludur.',
            'decimal_places.integer' => 'Ondalık basamak sayısı tam sayı olmalıdır.',
            'decimal_places.min' => 'Ondalık basamak sayısı negatif olamaz.',
            'decimal_places.max' => 'Ondalık basamak sayısı en fazla 4 olabilir.',
            'thousand_separator.max' => 'Binlik ayırıcı tek karakter olmalıdır.',
            'decimal_separator.required' => 'Ondalık ayırıcı zorunludur.',
            'decimal_separator.max' => 'Ondalık ayırıcı tek karakter olmalıdır.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Para birimi kodunu büyük harfe çevir
        if ($this->filled('code')) {
            $this->merge([
                'code' => strtoupper($this->code)
            ]);
        }

        // Eğer varsayılan olarak işaretlenmişse, diğerlerini varsayılan olmaktan çıkar
        if ($this->is_default) {
            \App\Modules\Currencies\Models\Currency::where('is_default', true)
                ->where('id', '!=', $this->route('currency'))
                ->update(['is_default' => false]);
        }
    }
}
