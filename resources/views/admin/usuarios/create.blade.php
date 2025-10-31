@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif


@section('content')
<section class="content-header py-3" style="background: #e7f1ff;">
    <div class="container-fluid d-flex align-items-center gap-3">
        <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
            <i class="fas fa-user-plus me-2"></i> Crear nuevo usuario
        </h1>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary shadow-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</section>

<div class="content px-4 py-4"
     style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            {!! Form::open(['route' => 'admin.usuarios.store', 'novalidate' => true]) !!}
                <div class="row g-3">
                    @include('admin.usuarios.fields')
                </div>

                <div class="text-end mt-4">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary me-2']) !!}
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection
