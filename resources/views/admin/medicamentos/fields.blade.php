
<div class="form-group col-md-6">
    {!! Form::label('nombre', 'Nombre del medicamento:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required', 'placeholder' => 'Ej. Paracetamol']) !!}
</div>

<div class="form-group col-md-6">
    {!! Form::label('presentacion', 'Presentación:') !!}
    {!! Form::text('presentacion', null, ['class' => 'form-control', 'placeholder' => 'Ej. Tabletas 500mg']) !!}
</div>

<div class="form-group col-md-12">
    {!! Form::label('indicaciones', 'Indicaciones:') !!}
    {!! Form::textarea('indicaciones', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Modo de uso o recomendaciones']) !!}
</div>

<div class="form-group col-md-12">
    {!! Form::label('efectosSecundarios', 'Efectos secundarios:') !!}
    {!! Form::textarea('efectosSecundarios', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Posibles efectos secundarios']) !!}
</div>

{{-- Imagen --}}
<div class="form-group col-md-6">
    {!! Form::label('imagenMedicamento', 'Imagen del medicamento:') !!}
    {!! Form::file('imagenMedicamento', ['class' => 'form-control', 'id' => 'imagenMedicamento']) !!}

    {{-- Previsualización (solo si existe al editar) --}}
    @if(isset($medicamento) && $medicamento->imagenMedicamento)
        <div class="mt-3">
            <img src="{{ asset('storage/' . $medicamento->imagenMedicamento) }}"
                 alt="Imagen actual"
                 class="img-thumbnail rounded"
                 style="max-width: 200px;">
        </div>
    @endif

    {{-- Previsualización al seleccionar nueva imagen --}}
    <div class="mt-3" id="preview-container" style="display:none;">
        <img id="preview-image" class="img-thumbnail rounded" style="max-width:200px;">
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('imagenMedicamento')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');

        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                previewImage.src = reader.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            previewImage.src = '';
        }
    });
</script>
@endpush
