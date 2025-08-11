@extends('layouts.admin')

@section('title', 'Siparişler')
@section('page-title', 'Siparişler')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Siparişler</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Sipariş Listesi</h5>
        </div>

        <div class="text-center py-5">
            <i class="ki-duotone ki-basket fs-4x text-muted mb-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
            <p class="text-muted">Sipariş modülü yakında aktif olacaktır.</p>
        </div>
    </div>
</div>
@endsection
