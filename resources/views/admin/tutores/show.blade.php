@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-semibold text-primary">Detalles del Tutor</h1>
        <a href="{{ route('admin.tutores.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Regresar
        </a>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                @include('admin.tutores.show_fields')
            </div>
        </div>
    </div>
</div>
@endsection
