@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dil Detayı: {{ $language->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">ID</th>
                                    <td>
                                        {{ $language->id }}
                                        @if($language->code == 'tr')
                                            <span class="badge badge-info ml-1">Varsayılan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dil Adı</th>
                                    <td>{{ $language->name }}</td>
                                </tr>
                                <tr>
                                    <th>Dil Kodu</th>
                                    <td><code>{{ $language->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Locale</th>
                                    <td><code>{{ $language->locale }}</code></td>
                                </tr>
                                <tr>
                                    <th>Bayrak</th>
                                    <td>
                                        @if($language->image)
                                            <img src="{{ $language->image->url }}" alt="{{ $language->name }}" 
                                                 class="img-thumbnail" style="max-width: 64px;">
                                        @else
                                            <span class="text-muted">Bayrak yüklenmemiş</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sıralama</th>
                                    <td>{{ $language->order }}</td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        @if($language->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma Tarihi</th>
                                    <td>{{ $language->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $language->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Dil Dosyaları</h5>
                            @php
                                $langPath = resource_path('lang/' . $language->code);
                                $fileExists = file_exists($langPath);
                            @endphp
                            
                            @if($fileExists)
                                <div class="alert alert-success">
                                    <i class="icon fas fa-check"></i>
                                    Dil dosyaları mevcut
                                </div>
                                
                                @php
                                    $files = glob($langPath . '/*.php');
                                @endphp
                                
                                @if(count($files) > 0)
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Dosya</th>
                                            <th width="150">Boyut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                        <tr>
                                            <td><code>{{ basename($file) }}</code></td>
                                            <td>{{ number_format(filesize($file) / 1024, 2) }} KB</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="icon fas fa-exclamation-triangle"></i>
                                    Dil dosyaları henüz oluşturulmamış
                                </div>
                            @endif

                            <h5 class="mt-4">Kullanım Örnekleri</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>PHP Kodu:</h6>
                                    <pre><code>// Dili ayarla
app()->setLocale('{{ $language->code }}');

// Çeviri kullan
__('messages.welcome');
trans('auth.failed');</code></pre>

                                    <h6 class="mt-3">Blade Şablonu:</h6>
                                    <pre><code>&#123;&#123; __('messages.welcome') &#125;&#125;
&#64;lang('auth.failed')</code></pre>

                                    <h6 class="mt-3">URL:</h6>
                                    <pre><code>// Dil parametresi ile
{{ url('/') }}/{{ $language->code }}/dashboard

// Subdomain ile
{{ $language->code }}.example.com</code></pre>
                                </div>
                            </div>

                            @if($language->code == 'tr')
                            <div class="alert alert-warning mt-3">
                                <i class="icon fas fa-exclamation-triangle"></i>
                                Bu varsayılan dildir ve silinemez. Sistemde en az bir aktif dil bulunmalıdır.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
