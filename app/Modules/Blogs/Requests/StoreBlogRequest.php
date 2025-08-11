<?php

namespace App\Modules\Blogs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('blogs.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'views' => 'nullable|integer|min:0',
            'author_id' => 'nullable|exists:users,id',
            'allow_comments' => 'boolean',
            'status' => 'required|in:draft,published,scheduled,archived'
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
            'title.required' => 'Blog başlığı zorunludur.',
            'title.max' => 'Blog başlığı en fazla 255 karakter olabilir.',
            'content.required' => 'Blog içeriği zorunludur.',
            'slug.unique' => 'Bu slug daha önce kullanılmış.',
            'excerpt.max' => 'Özet en fazla 500 karakter olabilir.',
            'featured_image.image' => 'Sadece resim dosyası yükleyebilirsiniz.',
            'featured_image.max' => 'Resim boyutu en fazla 2MB olabilir.',
            'category_id.exists' => 'Seçilen kategori bulunamadı.',
            'author_id.exists' => 'Seçilen yazar bulunamadı.',
            'status.in' => 'Geçersiz durum seçimi.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Slug yoksa title'dan oluştur
        if (!$this->filled('slug') && $this->filled('title')) {
            $this->merge([
                'slug' => \Str::slug($this->title)
            ]);
        }

        // Yazar ID'si yoksa giriş yapan kullanıcıyı ata
        if (!$this->filled('author_id')) {
            $this->merge([
                'author_id' => auth()->id()
            ]);
        }

        // Yayınlanma tarihi yoksa ve yayınlanacaksa şimdiki zamanı ata
        if (!$this->filled('published_at') && $this->is_published) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }
}
