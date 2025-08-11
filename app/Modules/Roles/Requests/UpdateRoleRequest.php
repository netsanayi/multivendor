<?php

namespace App\Modules\Roles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('roles.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $role = $this->route('role');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id),
            ],
            'description' => 'nullable|string|max:500',
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
            'permissions.array' => 'İzinler dizi formatında olmalıdır.',
            'permissions.*.exists' => 'Seçilen izin bulunamadı.',
        ];
    }
}
