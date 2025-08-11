@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Yeni Mesaj</h2>
                    <p class="mt-1 text-sm text-gray-600">Yeni bir konuşma başlatın</p>
                </div>

                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Recipient -->
                        <div>
                            <label for="recipient_id" class="block text-sm font-medium text-gray-700">Alıcı</label>
                            <select name="recipient_id" id="recipient_id" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('recipient_id') border-red-500 @enderror">
                                <option value="">Kullanıcı seçin</option>
                                @foreach($users as $user)
                                    @if($user->id != auth()->id())
                                        <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                            @if($user->hasRole('vendor'))
                                                - Satıcı
                                            @elseif($user->hasRole('admin'))
                                                - Admin
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('recipient_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Konu (Opsiyonel)</label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject') }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('subject') border-red-500 @enderror"
                                placeholder="Mesaj konusu">
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Mesajınız</label>
                            <textarea name="content" id="content" rows="6" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('content') border-red-500 @enderror"
                                placeholder="Mesajınızı yazın...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Tipi</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="text" checked
                                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                    <span class="ml-2">Normal Mesaj</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="type" value="offer"
                                        class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                    <span class="ml-2">Teklif</span>
                                </label>
                            </div>
                        </div>

                        <!-- Offer Details (shown only when offer is selected) -->
                        <div id="offer-details" class="hidden space-y-4">
                            <div>
                                <label for="offer_amount" class="block text-sm font-medium text-gray-700">Teklif Tutarı</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₺</span>
                                    </div>
                                    <input type="number" name="offer_amount" id="offer_amount" step="0.01"
                                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label for="offer_description" class="block text-sm font-medium text-gray-700">Teklif Açıklaması</label>
                                <textarea name="offer_description" id="offer_description" rows="3"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Teklifinizin detaylarını açıklayın..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('messages.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            İptal
                        </a>
                        <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Mesajı Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const offerDetails = document.getElementById('offer-details');
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'offer') {
                offerDetails.classList.remove('hidden');
            } else {
                offerDetails.classList.add('hidden');
            }
        });
    });
});
</script>
@endsection