@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Aktivite Log Detayı #{{ $activity->id }}</h1>
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Genel Bilgiler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Genel Bilgiler</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Açıklama:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $activity->description }}
                            @if($activity->event)
                                <span class="badge bg-info ms-2">{{ $activity->event }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Log Adı:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-secondary">{{ $activity->log_name ?: 'default' }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Tarih:</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $activity->created_at->format('d.m.Y H:i:s') }}
                            <small class="text-muted">({{ $activity->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    
                    @if($activity->batch_uuid)
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Batch UUID:</strong>
                            </div>
                            <div class="col-md-9">
                                <code>{{ $activity->batch_uuid }}</code>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Konu Bilgileri -->
            @if($activity->subject)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Konu Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Tip:</strong>
                            </div>
                            <div class="col-md-9">
                                <code>{{ $activity->subject_type }}</code>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>ID:</strong>
                            </div>
                            <div class="col-md-9">
                                #{{ $activity->subject_id }}
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Model:</strong>
                            </div>
                            <div class="col-md-9">
                                {{ class_basename($activity->subject_type) }}
                                @if($activity->subject)
                                    <a href="#" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt"></i> Görüntüle
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Özellikler -->
            @if($activity->properties && count($activity->properties) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Özellikler</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($activity->properties['old']) && isset($activity->properties['attributes']))
                            <!-- Değişiklik tablosu -->
                            <h6 class="mb-3">Değişiklikler:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Alan</th>
                                            <th>Eski Değer</th>
                                            <th>Yeni Değer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $old = $activity->properties['old'];
                                            $new = $activity->properties['attributes'];
                                            $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
                                        @endphp
                                        @foreach($allKeys as $key)
                                            @php
                                                $oldValue = $old[$key] ?? null;
                                                $newValue = $new[$key] ?? null;
                                            @endphp
                                            @if($oldValue != $newValue)
                                                <tr>
                                                    <td><strong>{{ $key }}</strong></td>
                                                    <td>
                                                        <span class="text-danger">
                                                            {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">
                                                            {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- JSON görüntüleme -->
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Kullanıcı Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    @if($activity->causer)
                        <div class="mb-3">
                            <strong>Kullanıcı:</strong><br>
                            {{ $activity->causer->name }}
                        </div>
                        
                        <div class="mb-3">
                            <strong>E-posta:</strong><br>
                            {{ $activity->causer->email }}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Kullanıcı ID:</strong><br>
                            #{{ $activity->causer_id }}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Kullanıcı Tipi:</strong><br>
                            <code>{{ $activity->causer_type }}</code>
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-robot"></i> Sistem tarafından gerçekleştirildi
                        </p>
                    @endif
                </div>
            </div>

            <!-- İstemci Bilgileri -->
            @if(isset($activity->properties['ip']) || isset($activity->properties['user_agent']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">İstemci Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($activity->properties['ip']))
                            <div class="mb-3">
                                <strong>IP Adresi:</strong><br>
                                <code>{{ $activity->properties['ip'] }}</code>
                            </div>
                        @endif
                        
                        @if(isset($activity->properties['user_agent']))
                            <div>
                                <strong>User Agent:</strong><br>
                                <small class="text-muted">{{ $activity->properties['user_agent'] }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- İlgili Loglar -->
            @if($activity->batch_uuid)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">İlgili Loglar</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $relatedCount = \Spatie\Activitylog\Models\Activity::where('batch_uuid', $activity->batch_uuid)
                                ->where('id', '!=', $activity->id)
                                ->count();
                        @endphp
                        
                        @if($relatedCount > 0)
                            <p>Bu işlemle ilgili <strong>{{ $relatedCount }}</strong> adet başka log bulundu.</p>
                            <a href="{{ route('admin.activity-log.index', ['batch_uuid' => $activity->batch_uuid]) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-list"></i> İlgili Logları Görüntüle
                            </a>
                        @else
                            <p class="text-muted mb-0">İlgili başka log bulunamadı.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    pre code {
        display: block;
        overflow-x: auto;
        padding: 0.5em;
        background: #f8f9fa;
        color: #333;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush
