@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="flex h-screen">
                <!-- Conversations List -->
                <div class="w-1/3 border-r">
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Mesajlar</h2>
                            <a href="{{ route('messages.create') }}" class="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="overflow-y-auto h-full">
                        @foreach($threads as $t)
                        <a href="{{ route('messages.show', $t) }}" class="block hover:bg-gray-50 px-4 py-3 border-b {{ $t->id == $thread->id ? 'bg-gray-100' : '' }}">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium text-sm">
                                            {{ substr($t->participants->where('id', '!=', auth()->id())->first()->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $t->participants->where('id', '!=', auth()->id())->first()->name ?? 'Kullanıcı' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $t->last_message_at ? $t->last_message_at->diffForHumans() : $t->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $t->messages->last()->content ?? 'Mesaj yok' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Message Area -->
                <div class="flex-1 flex flex-col">
                    <!-- Header -->
                    <div class="p-4 border-b flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-medium text-sm">
                                    {{ substr($otherUser->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $otherUser->name }}</h3>
                                @if($thread->subject)
                                    <p class="text-sm text-gray-500">{{ $thread->subject }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if(!$thread->is_starred)
                                <form method="POST" action="{{ route('messages.star', $thread) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-400 hover:text-yellow-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('messages.star', $thread) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-yellow-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('messages.archive', $thread) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @foreach($messages as $message)
                        <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <div class="{{ $message->sender_id == auth()->id() ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900' }} rounded-lg px-4 py-2">
                                    @if($message->type == 'offer')
                                        <div class="border-2 {{ $message->sender_id == auth()->id() ? 'border-indigo-400' : 'border-gray-300' }} rounded p-2 mb-2">
                                            <p class="font-semibold">Teklif: ₺{{ number_format($message->offer_amount, 2) }}</p>
                                            @if($message->offer_description)
                                                <p class="text-sm mt-1">{{ $message->offer_description }}</p>
                                            @endif
                                            @if($message->sender_id != auth()->id() && $message->offer_status == 'pending')
                                                <div class="mt-2 flex space-x-2">
                                                    <form method="POST" action="{{ route('messages.offer.accept', [$thread, $message]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs bg-green-500 text-white px-2 py-1 rounded">Kabul Et</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('messages.offer.reject', [$thread, $message]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs bg-red-500 text-white px-2 py-1 rounded">Reddet</button>
                                                    </form>
                                                </div>
                                            @elseif($message->offer_status == 'accepted')
                                                <p class="text-xs mt-1 text-green-600">✓ Kabul edildi</p>
                                            @elseif($message->offer_status == 'rejected')
                                                <p class="text-xs mt-1 text-red-600">✗ Reddedildi</p>
                                            @endif
                                        </div>
                                    @endif
                                    <p>{{ $message->content }}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $message->created_at->format('H:i') }}
                                    @if($message->is_read)
                                        <span class="ml-1">✓✓</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Reply Form -->
                    <div class="p-4 border-t">
                        <form method="POST" action="{{ route('messages.send', $thread) }}" class="flex space-x-2">
                            @csrf
                            <input type="text" name="content" 
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Mesajınızı yazın..." required>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection