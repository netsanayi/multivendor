@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Şifrenizi doğrulayın
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Bu güvenli bir alandır. Devam etmek için lütfen şifrenizi doğrulayın.
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.confirm') }}">
            @csrf
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                       placeholder="Şifrenizi girin">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Doğrula
                </button>
            </div>
            
            @if (Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Şifrenizi mi unuttunuz?
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection