<?php

namespace App\Modules\Roles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('roles.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Rol adı zorunludur.',
            'name.unique' => 'Bu rol adı zaten kullanılıyor.',
            'name.max' => 'Rol adı en fazla 255 karakter olabilir.',
            'description.max' => 'Açıklama en fazla 500 karakter olabilir.',
            'guard_name.required' => 'Guard tipi zorunludur.',
            'guard_name.in' => 'Geçersiz guard tipi.',
            'permissions.array' => 'İzinler dizi formatında olmalıdır.',
            'permissions.*.exists' => 'Seçilen izin bulunamadı.',
        ];
    }
}
