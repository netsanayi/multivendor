@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Bildirim Ayarları</h2>
                    <p class="mt-1 text-sm text-gray-600">Hangi bildirimleri almak istediğinizi seçin</p>
                </div>

                <form method="POST" action="{{ route('notifications.settings.update') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Email Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">E-posta Bildirimleri</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_notifications[order_updates]" value="1" 
                                        {{ old('email_notifications.order_updates', $settings->email_notifications['order_updates'] ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Sipariş güncellemeleri</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_notifications[new_messages]" value="1"
                                        {{ old('email_notifications.new_messages', $settings->email_notifications['new_messages'] ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Yeni mesajlar</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_notifications[product_updates]" value="1"
                                        {{ old('email_notifications.product_updates', $settings->email_notifications['product_updates'] ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Ürün güncellemeleri</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_notifications[promotions]" value="1"
                                        {{ old('email_notifications.promotions', $settings->email_notifications['promotions'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Promosyonlar ve kampanyalar</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_notifications[newsletter]" value="1"
                                        {{ old('email_notifications.newsletter', $settings->email_notifications['newsletter'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Haftalık bülten</span>
                                </label>
                            </div>
                        </div>

                        <!-- Push Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Anlık Bildirimler</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="push_notifications[enabled]" value="1"
                                        {{ old('push_notifications.enabled', $settings->push_notifications['enabled'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">Tarayıcı bildirimlerini etkinleştir</span>
                                </label>
                                
                                <div class="ml-6 space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="push_notifications[order_updates]" value="1"
                                            {{ old('push_notifications.order_updates', $settings->push_notifications['order_updates'] ?? true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">Sipariş güncellemeleri</span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="push_notifications[new_messages]" value="1"
                                            {{ old('push_notifications.new_messages', $settings->push_notifications['new_messages'] ?? true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">Yeni mesajlar</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">SMS Bildirimleri</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="sms_notifications[enabled]" value="1"
                                        {{ old('sms_notifications.enabled', $settings->sms_notifications['enabled'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2">SMS bildirimlerini etkinleştir</span>
                                </label>
                                
                                <div class="ml-6 space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="sms_notifications[order_shipped]" value="1"
                                            {{ old('sms_notifications.order_shipped', $settings->sms_notifications['order_shipped'] ?? true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">Sipariş kargoya verildiğinde</span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="sms_notifications[order_delivered]" value="1"
                                            {{ old('sms_notifications.order_delivered', $settings->sms_notifications['order_delivered'] ?? true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">Sipariş teslim edildiğinde</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Frequency -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Bildirim Sıklığı</h3>
                            <select name="notification_frequency" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="instant" {{ old('notification_frequency', $settings->notification_frequency ?? 'instant') == 'instant' ? 'selected' : '' }}>Anında</option>
                                <option value="hourly" {{ old('notification_frequency', $settings->notification_frequency ?? 'instant') == 'hourly' ? 'selected' : '' }}>Saatlik özet</option>
                                <option value="daily" {{ old('notification_frequency', $settings->notification_frequency ?? 'instant') == 'daily' ? 'selected' : '' }}>Günlük özet</option>
                                <option value="weekly" {{ old('notification_frequency', $settings->notification_frequency ?? 'instant') == 'weekly' ? 'selected' : '' }}>Haftalık özet</option>
                            </select>
                        </div>

                        <!-- Quiet Hours -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Sessiz Saatler</h3>
                            <label class="flex items-center mb-3">
                                <input type="checkbox" name="quiet_hours[enabled]" value="1"
                                    {{ old('quiet_hours.enabled', $settings->quiet_hours['enabled'] ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2">Sessiz saatleri etkinleştir</span>
                            </label>
                            
                            <div class="flex space-x-4">
                                <div>
                                    <label for="quiet_hours_start" class="block text-sm font-medium text-gray-700">Başlangıç</label>
                                    <input type="time" name="quiet_hours[start]" id="quiet_hours_start"
                                        value="{{ old('quiet_hours.start', $settings->quiet_hours['start'] ?? '22:00') }}"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="quiet_hours_end" class="block text-sm font-medium text-gray-700">Bitiş</label>
                                    <input type="time" name="quiet_hours[end]" id="quiet_hours_end"
                                        value="{{ old('quiet_hours.end', $settings->quiet_hours['end'] ?? '08:00') }}"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('notifications.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            İptal
                        </a>
                        <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ayarları Kaydet
                        </button>
                    </div>
                </form>

                @if(session('status'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection