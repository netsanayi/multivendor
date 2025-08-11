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
                        @if($threads->isEmpty())
                            <div class="p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Henüz mesaj yok</h3>
                                <p class="mt-1 text-sm text-gray-500">Yeni bir konuşma başlatın.</p>
                                <div class="mt-6">
                                    <a href="{{ route('messages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Yeni Mesaj
                                    </a>
                                </div>
                            </div>
                        @else
                            @foreach($threads as $thread)
                            <a href="{{ route('messages.show', $thread) }}" class="block hover:bg-gray-50 px-4 py-3 border-b {{ request()->route('thread') && request()->route('thread')->id == $thread->id ? 'bg-gray-100' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 font-medium text-sm">
                                                {{ substr($thread->participants->where('id', '!=', auth()->id())->first()->name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $thread->participants->where('id', '!=', auth()->id())->first()->name ?? 'Kullanıcı' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $thread->last_message_at ? $thread->last_message_at->diffForHumans() : $thread->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $thread->messages->last()->content ?? 'Mesaj yok' }}
                                        </p>
                                        @if($thread->unread_count > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $thread->unread_count }} yeni
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Message Area -->
                <div class="flex-1 flex flex-col">
                    @if(!$threads->isEmpty())
                        <div class="flex-1 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900">Bir konuşma seçin</h3>
                                <p class="mt-1 text-sm text-gray-500">Sol taraftan bir konuşma seçerek mesajlaşmaya başlayın.</p>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900">Mesajlarınız burada görünecek</h3>
                                <p class="mt-1 text-sm text-gray-500">Yeni bir konuşma başlatarak mesajlaşmaya başlayın.</p>
                                <div class="mt-6">
                                    <a href="{{ route('messages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Yeni Mesaj Başlat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection