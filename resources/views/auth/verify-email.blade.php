@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                E-posta adresinizi doğrulayın
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Devam etmeden önce, lütfen e-posta adresinizi doğrulayın.
            </p>
        </div>
        
        @if (session('status') == 'verification-link-sent')
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Yeni bir doğrulama bağlantısı e-posta adresinize gönderildi.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="mt-8 space-y-6">
            <p class="text-sm text-gray-600">
                E-postanıza gönderilen doğrulama bağlantısına tıklayarak hesabınızı aktif hale getirebilirsiniz.
                E-postayı almadıysanız, yeni bir doğrulama e-postası göndermek için aşağıdaki butona tıklayın.
            </p>
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Doğrulama E-postasını Tekrar Gönder
                </button>
            </form>
            
            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Çıkış Yap
                </button>
            </form>
        </div>
    </div>
</div>
@endsection