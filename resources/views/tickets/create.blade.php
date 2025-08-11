@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Yeni Destek Talebi</h2>
                    <p class="mt-1 text-sm text-gray-600">Aşağıdaki formu doldurarak yeni bir destek talebi oluşturabilirsiniz.</p>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Konu</label>
                            <input type="text" name="subject" id="subject" required
                                value="{{ old('subject') }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('subject') border-red-500 @enderror"
                                placeholder="Sorunuzu kısaca özetleyin">
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category" id="category" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('category') border-red-500 @enderror">
                                <option value="">Kategori seçin</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>Genel</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Teknik Destek</option>
                                <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Ödeme / Faturalama</option>
                                <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>Hesap</option>
                                <option value="product" {{ old('category') == 'product' ? 'selected' : '' }}>Ürün</option>
                                <option value="shipping" {{ old('category') == 'shipping' ? 'selected' : '' }}>Kargo</option>
                                <option value="return" {{ old('category') == 'return' ? 'selected' : '' }}>İade / Değişim</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Diğer</option>
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Öncelik</label>
                            <select name="priority" id="priority" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('priority') border-red-500 @enderror">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Orta</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                            </select>
                            @error('priority')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Mesajınız</label>
                            <textarea name="message" id="message" rows="6" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('message') border-red-500 @enderror"
                                placeholder="Sorununuzu detaylı bir şekilde açıklayın...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Ekler (Opsiyonel)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Dosya yükle</span>
                                            <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept="image/*,.pdf,.doc,.docx">
                                        </label>
                                        <p class="pl-1">veya sürükle bırak</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC 10MB'a kadar</p>
                                </div>
                            </div>
                            @error('attachments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('tickets.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            İptal
                        </a>
                        <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Talebi Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection