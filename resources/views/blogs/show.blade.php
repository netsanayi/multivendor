@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Blog Yazısı Detayı</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 class="h3 mb-3">{{ $blog->title }}</h1>
                            
                            <div class="mb-3">
                                @if($blog->status)
                                    <span class="badge badge-success">Yayında</span>
                                @else
                                    <span class="badge badge-warning">Taslak</span>
                                @endif
                                <span class="text-muted ml-2">
                                    <i class="fas fa-calendar"></i> {{ $blog->created_at->format('d.m.Y H:i') }}
                                </span>
                            </div>

                            <div class="blog-content">
                                {!! $blog->description !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Blog Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th>ID:</th>
                                            <td>{{ $blog->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Slug:</th>
                                            <td>{{ $blog->slug }}</td>
                                        </tr>
                                        <tr>
                                            <th>Oluşturulma:</th>
                                            <td>{{ $blog->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Güncelleme:</th>
                                            <td>{{ $blog->updated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">SEO Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Meta Başlık:</strong><br>
                                    {{ $blog->meta_title ?: $blog->title }}</p>
                                    
                                    <p><strong>Meta Açıklama:</strong><br>
                                    {{ $blog->meta_description ?: Str::limit(strip_tags($blog->description), 160) }}</p>
                                    
                                    <p class="mb-0"><strong>Meta Anahtar Kelimeler:</strong><br>
                                    {{ $blog->meta_keywords ?: '-' }}</p>
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

@push('styles')
<style>
    .blog-content {
        font-size: 1.1em;
        line-height: 1.8;
    }
    .blog-content img {
        max-width: 100%;
        height: auto;
        margin: 1rem 0;
    }
    .blog-content h1, .blog-content h2, .blog-content h3, 
    .blog-content h4, .blog-content h5, .blog-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .blog-content p {
        margin-bottom: 1rem;
    }
    .blog-content ul, .blog-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    .blog-content blockquote {
        border-left: 4px solid #ddd;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
    }
    .blog-content table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    .blog-content table th,
    .blog-content table td {
        border: 1px solid #ddd;
        padding: 0.5rem;
    }
</style>
@endpush
