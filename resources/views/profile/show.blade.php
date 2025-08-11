@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <!-- Profile Information -->
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900">Profil Bilgileri</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Hesabınızın profil bilgilerini ve e-posta adresini güncelleyin.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form method="POST" action="{{ route('user-profile-information.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Name -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                                        <input type="text" name="name" id="name" autocomplete="name"
                                            value="{{ old('name', Auth::user()->name) }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="email" class="block text-sm font-medium text-gray-700">E-posta</label>
                                        <input type="email" name="email" id="email" autocomplete="email"
                                            value="{{ old('email', Auth::user()->email) }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        @error('email')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                                @if (session('status') === 'profile-information-updated')
                                    <div class="mr-3 text-sm text-gray-600">Kaydedildi.</div>
                                @endif
                                
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                    Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="hidden sm:block">
                    <div class="py-8">
                        <div class="border-t border-gray-200"></div>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium text-gray-900">Şifre Güncelle</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Hesabınızın güvenliği için uzun, rastgele bir şifre kullandığınızdan emin olun.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form method="POST" action="{{ route('user-password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                    <div class="grid grid-cols-6 gap-6">
                                        <!-- Current Password -->
                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="current_password" class="block text-sm font-medium text-gray-700">Mevcut Şifre</label>
                                            <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            @error('current_password', 'updatePassword')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- New Password -->
                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="password" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
                                            <input type="password" name="password" id="password" autocomplete="new-password"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            @error('password', 'updatePassword')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-span-6 sm:col-span-4">
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Şifre Tekrar</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                                    @if (session('status') === 'password-updated')
                                        <div class="mr-3 text-sm text-gray-600">Kaydedildi.</div>
                                    @endif
                                    
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                        Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="hidden sm:block">
                    <div class="py-8">
                        <div class="border-t border-gray-200"></div>
                    </div>
                </div>

                <!-- Two Factor Authentication -->
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium text-gray-900">İki Faktörlü Kimlik Doğrulama</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    İki faktörlü kimlik doğrulamayı kullanarak hesabınıza ek güvenlik ekleyin.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-lg">
                                @if (!Auth::user()->two_factor_secret)
                                    <div class="text-sm text-gray-600">
                                        İki faktörlü kimlik doğrulama etkin değil.
                                    </div>
                                    
                                    <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="mt-5">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                            Etkinleştir
                                        </button>
                                    </form>
                                @else
                                    <div class="text-sm text-gray-600">
                                        İki faktörlü kimlik doğrulama etkin.
                                    </div>
                                    
                                    <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="mt-5">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 disabled:opacity-25 transition">
                                            Devre Dışı Bırak
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="hidden sm:block">
                    <div class="py-8">
                        <div class="border-t border-gray-200"></div>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium text-gray-900">Hesabı Sil</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Hesabınızı kalıcı olarak silin.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-lg">
                                <div class="max-w-xl text-sm text-gray-600">
                                    Hesabınız silindiğinde, tüm kaynaklarınız ve verileriniz kalıcı olarak silinecektir. Hesabınızı silmeden önce, saklamak istediğiniz tüm verileri veya bilgileri indirin.
                                </div>

                                <div class="mt-5">
                                    <button type="button" onclick="confirm('Hesabınızı silmek istediğinizden emin misiniz?') && document.getElementById('delete-account-form').submit()"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 disabled:opacity-25 transition">
                                        Hesabı Sil
                                    </button>
                                    
                                    <form id="delete-account-form" method="POST" action="{{ route('current-user.destroy') }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection