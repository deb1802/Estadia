@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush

@section('content')
<section class="content-header py-3" style="background: #e7f1ff;">
    <div class="container-fluid d-flex align-items-center gap-3">
        <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
            <i class="fas fa-users me-2"></i> Gestión de usuarios
        </h1>
        <a class="btn btn-primary shadow-sm btn-lg d-flex align-items-center"
           href="{{ route('admin.usuarios.create') }}">
            <i class="fas fa-user-plus me-2"></i> Crear nuevo usuario
        </a>
    </div>
</section>

<div class="content px-4 py-3" style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">
    {{-- Mensajes flash --}}
    @include('flash::message')

    {{-- Contenedor principal de la tabla --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            {{-- ✅ Tabla CRUD estilizada --}}
            @include('admin.usuarios.table')
        </div>
    </div>
</div>
@endsection
