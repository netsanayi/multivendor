@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                İki Faktörlü Kimlik Doğrulama
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Lütfen kimlik doğrulama uygulamanızdan 6 haneli kodu girin veya kurtarma kodlarınızdan birini kullanın.
            </p>
        </div>
        
        <div x-data="{ recovery: false }">
            <form class="mt-8 space-y-6" method="POST" action="{{ route('two-factor.login') }}">
                @csrf
                
                <div x-show="! recovery">
                    <label for="code" class="block text-sm font-medium text-gray-700">Doğrulama Kodu</label>
                    <input id="code" name="code" type="text" inputmode="numeric" 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('code') border-red-500 @enderror" 
                           placeholder="123456"
                           autofocus x-ref="code">
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="recovery">
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700">Kurtarma Kodu</label>
                    <input id="recovery_code" name="recovery_code" type="text" 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('recovery_code') border-red-500 @enderror" 
                           placeholder="xxxxx-xxxxx"
                           x-ref="recovery_code">
                    @error('recovery_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Giriş Yap
                    </button>
                </div>
                
                <div class="text-center">
                    <button type="button" 
                            class="font-medium text-indigo-600 hover:text-indigo-500"
                            x-show="! recovery"
                            x-on:click="
                                recovery = true;
                                $nextTick(() => { $refs.recovery_code.focus() })
                            ">
                        Kurtarma kodu kullan
                    </button>
                    
                    <button type="button" 
                            class="font-medium text-indigo-600 hover:text-indigo-500"
                            x-show="recovery"
                            x-on:click="
                                recovery = false;
                                $nextTick(() => { $refs.code.focus() })
                            ">
                        Doğrulama kodu kullan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection