<div class="form-group col-md-6">
  {!! Form::label('nombre', 'Nombre del medicamento:') !!}
  {!! Form::text('nombre', old('nombre', $medicamento->nombre ?? null), [
    'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
    'required' => true,
    'maxlength' => 100,
    'placeholder' => 'Ej. Paracetamol'
  ]) !!}
  @error('nombre')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group col-md-6">
  {!! Form::label('presentacion', 'Presentación:') !!}
  {!! Form::text('presentacion', old('presentacion', $medicamento->presentacion ?? null), [
    'class' => 'form-control' . ($errors->has('presentacion') ? ' is-invalid' : ''),
    'required' => true,
    'maxlength' => 50,
    'placeholder' => 'Ej. Tabletas 500mg'
  ]) !!}
  @error('presentacion')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group col-md-12">
  {!! Form::label('indicaciones', 'Indicaciones:') !!}
  {!! Form::textarea('indicaciones', old('indicaciones', $medicamento->indicaciones ?? null), [
    'class' => 'form-control' . ($errors->has('indicaciones') ? ' is-invalid' : ''),
    'required' => true,
    'rows' => 3,
    'placeholder' => 'Modo de uso o recomendaciones'
  ]) !!}
  @error('indicaciones')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group col-md-12">
  {!! Form::label('efectosSecundarios', 'Efectos secundarios:') !!}
  {!! Form::textarea('efectosSecundarios', old('efectosSecundarios', $medicamento->efectosSecundarios ?? null), [
    'class' => 'form-control' . ($errors->has('efectosSecundarios') ? ' is-invalid' : ''),
    'required' => true,
    'rows' => 3,
    'placeholder' => 'Posibles efectos secundarios'
  ]) !!}
  @error('efectosSecundarios')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Imagen (opcional) --}}
<div class="form-group col-md-6">
  {!! Form::label('imagenMedicamento', 'Imagen del medicamento (opcional):') !!}
  {!! Form::file('imagenMedicamento', [
    'class' => 'form-control' . ($errors->has('imagenMedicamento') ? ' is-invalid' : ''),
    'id' => 'imagenMedicamento',
    'accept' => 'image/*'
  ]) !!}
  @error('imagenMedicamento')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror

  @if(isset($medicamento) && !empty($medicamento->imagenMedicamento))
    <div class="mt-3">
      <img src="{{ asset('storage/'.$medicamento->imagenMedicamento) }}"
           alt="Imagen actual"
           class="img-thumbnail rounded"
           style="max-width:200px;">
    </div>
  @endif

  <div class="mt-3" id="preview-container" style="display:none;">
    <img id="preview-image" class="img-thumbnail rounded" style="max-width:200px;">
  </div>
</div>

@push('scripts')
<script>
document.getElementById('imagenMedicamento')?.addEventListener('change', function (e) {
  const file = e.target.files?.[0];
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
