@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Ticket Header -->
                <div class="mb-6 border-b pb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $ticket->subject }}</h2>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                <span>Talep #{{ $ticket->id }}</span>
                                <span>•</span>
                                <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                                <span>•</span>
                                <span>{{ $ticket->user->name }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Status -->
                            @if($ticket->status == 'open')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Açık</span>
                            @elseif($ticket->status == 'answered')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Cevaplandı</span>
                            @elseif($ticket->status == 'pending')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Beklemede</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Kapalı</span>
                            @endif

                            <!-- Priority -->
                            @if($ticket->priority == 'high')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Yüksek Öncelik</span>
                            @elseif($ticket->priority == 'medium')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Orta Öncelik</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Düşük Öncelik</span>
                            @endif

                            <!-- Category -->
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($ticket->category) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="space-y-4 mb-6">
                    <!-- Original Message -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">{{ substr($ticket->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $ticket->user->name }}</h4>
                                    <time class="text-xs text-gray-500">{{ $ticket->created_at->format('d.m.Y H:i') }}</time>
                                </div>
                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</div>
                                
                                @if($ticket->attachments && count($ticket->attachments) > 0)
                                <div class="mt-3">
                                    <h5 class="text-xs font-medium text-gray-500 mb-2">Ekler:</h5>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($ticket->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ basename($attachment) }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Responses -->
                    @foreach($ticket->responses ?? [] as $response)
                    <div class="{{ $response->user_id == auth()->id() ? 'bg-blue-50' : 'bg-white' }} rounded-lg p-4 border">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full {{ $response->user->hasRole('admin') ? 'bg-indigo-500' : 'bg-gray-300' }} flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">{{ substr($response->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $response->user->name }}</h4>
                                        @if($response->user->hasRole('admin'))
                                            <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-100 text-indigo-800 rounded">Destek Ekibi</span>
                                        @endif
                                    </div>
                                    <time class="text-xs text-gray-500">{{ $response->created_at->format('d.m.Y H:i') }}</time>
                                </div>
                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ $response->message }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Reply Form -->
                @if($ticket->status != 'closed')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Yanıt Yaz</h3>
                    <form method="POST" action="{{ route('tickets.respond', $ticket) }}">
                        @csrf
                        <div>
                            <textarea name="message" rows="4" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('message') border-red-500 @enderror"
                                placeholder="Mesajınızı yazın...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4 flex justify-between">
                            <div class="flex space-x-2">
                                @if($ticket->status != 'closed')
                                <form method="POST" action="{{ route('tickets.close', $ticket) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Talebi kapatmak istediğinizden emin misiniz?')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Talebi Kapat
                                    </button>
                                </form>
                                @endif
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Yanıt Gönder
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="border-t pt-6">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600">Bu talep kapatılmıştır.</p>
                        @if(auth()->id() == $ticket->user_id)
                        <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Talebi Yeniden Aç
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection