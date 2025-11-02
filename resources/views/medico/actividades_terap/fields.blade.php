{{-- === Estilo mínimo para custom-file con error (no afecta funcionalidad) === --}}
<style>
  .custom-file-input.is-invalid ~ .custom-file-label{
    border-color:#dc3545;
  }
</style>

{{-- Título --}}
<div class="form-group col-sm-6">
  {!! Form::label('titulo', 'Título:') !!}
  {!! Form::text('titulo', old('titulo', $actividadesTerap->titulo ?? null), [
      'class' => 'form-control' . ($errors->has('titulo') ? ' is-invalid' : ''),
      'required' => true
  ]) !!}
  @error('titulo')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Tipo de Contenido --}}
<div class="form-group col-sm-6">
  {!! Form::label('tipoContenido', 'Tipo de Contenido:') !!}
  {!! Form::select('tipoContenido', [
      'audio' => 'Audio',
      'video' => 'Video',
      'lectura' => 'Lectura'
    ],
    old('tipoContenido', $actividadesTerap->tipoContenido ?? null),
    [
      'class' => 'form-control custom-select' . ($errors->has('tipoContenido') ? ' is-invalid' : ''),
      'placeholder' => 'Selecciona opción',
      'required' => true
    ]
  ) !!}
  @error('tipoContenido')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Categoría Terapéutica --}}
<div class="form-group col-sm-6">
  {!! Form::label('categoriaTerapeutica', 'Categoría Terapéutica:') !!}
  {!! Form::text('categoriaTerapeutica', old('categoriaTerapeutica', $actividadesTerap->categoriaTerapeutica ?? null), [
      'class' => 'form-control' . ($errors->has('categoriaTerapeutica') ? ' is-invalid' : ''),
      'required' => true,
      'placeholder' => 'Relajación, Respiración, Concentración'
  ]) !!}
  @error('categoriaTerapeutica')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Diagnóstico Dirigido --}}
<div class="form-group col-sm-6">
  {!! Form::label('diagnosticoDirigido', 'Diagnóstico Dirigido:') !!}
  {!! Form::text('diagnosticoDirigido', old('diagnosticoDirigido', $actividadesTerap->diagnosticoDirigido ?? null), [
      'class' => 'form-control' . ($errors->has('diagnosticoDirigido') ? ' is-invalid' : ''),
      'required' => true,
      'placeholder' => 'Ansiedad, Estrés, Depresión leve'
  ]) !!}
  @error('diagnosticoDirigido')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Nivel de Severidad --}}
<div class="form-group col-sm-6">
  {!! Form::label('nivelSeveridad', 'Nivel de Severidad:') !!}
  {!! Form::text('nivelSeveridad', old('nivelSeveridad', $actividadesTerap->nivelSeveridad ?? null), [
      'class' => 'form-control' . ($errors->has('nivelSeveridad') ? ' is-invalid' : ''),
      'required' => true,
      'placeholder' => 'Leve, Moderado, Severo'
  ]) !!}
  @error('nivelSeveridad')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Tipo de recurso (selector) --}}
<div class="form-group col-sm-6">
  {!! Form::label('modo_recurso', 'Tipo de recurso:') !!}
  {!! Form::select('modo_recurso', [
      'archivo' => 'Subir archivo',
      'link' => 'Enlace externo'
    ],
    old('modo_recurso'),
    [
      'class' => 'form-control' . ($errors->has('modo_recurso') ? ' is-invalid' : ''),
      'id' => 'modo_recurso',
      'placeholder' => 'Selecciona opción'
    ]
  ) !!}
  @error('modo_recurso')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Campo para subir archivo --}}
<div id="campo_archivo" class="form-group col-sm-6" style="display:none;">
  {!! Form::label('archivo', 'Archivo (imagen, audio, video o PDF):') !!}
  <div class="input-group">
    <div class="custom-file">
      {!! Form::file('archivo', [
          'class' => 'custom-file-input' . ($errors->has('archivo') ? ' is-invalid' : ''),
          'id' => 'archivo',
          'accept' => '.pdf,.mp3,.mp4,.avi,.mov,.jpg,.jpeg,.png'
      ]) !!}
      {!! Form::label('archivo', 'Elegir archivo', ['class' => 'custom-file-label']) !!}
    </div>
  </div>
  <small class="form-text text-muted">Formatos permitidos: PDF, MP3, MP4, AVI, MOV, JPG, PNG</small>
  @error('archivo')
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>

{{-- Campo para enlace externo --}}
<div id="campo_link" class="form-group col-sm-6" style="display:none;">
  {!! Form::label('link', 'Enlace del Recurso:') !!}
  {!! Form::text('link', old('link', $actividadesTerap->recurso ?? null), [
      'class' => 'form-control' . ($errors->has('link') ? ' is-invalid' : ''),
      'placeholder' => 'Ejemplo: https://www.youtube.com/watch?v=abcd1234'
  ]) !!}
  @error('link')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="clearfix"></div>

{{-- Script para alternar campos --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const selectModo   = document.getElementById('modo_recurso');
  const campoArchivo = document.getElementById('campo_archivo');
  const campoLink    = document.getElementById('campo_link');

  function toggleCampos() {
    if (!selectModo) return;
    if (selectModo.value === 'archivo') {
      campoArchivo.style.display = 'block';
      campoLink.style.display = 'none';
    } else if (selectModo.value === 'link') {
      campoArchivo.style.display = 'none';
      campoLink.style.display = 'block';
    } else {
      campoArchivo.style.display = 'none';
      campoLink.style.display = 'none';
    }
  }

  if (selectModo) {
    selectModo.addEventListener('change', toggleCampos);
    // Intenta deducir el modo desde el "old" para mantener estado tras error
    const oldLink = @json(old('link'));
    const oldArchivo = @json(old('archivo'));
    if (selectModo.value === '' && (oldLink || oldArchivo)) {
      selectModo.value = oldLink ? 'link' : 'archivo';
    }
    toggleCampos();
  }
});
</script>
