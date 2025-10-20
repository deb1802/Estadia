<!-- Título -->
<div class="form-group col-sm-6">
    {!! Form::label('titulo', 'Título:') !!}
    {!! Form::text('titulo', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Tipo de Contenido -->
<div class="form-group col-sm-6">
    {!! Form::label('tipoContenido', 'Tipo de Contenido:') !!}
    {!! Form::select('tipoContenido', [
        'audio' => 'Audio',
        'video' => 'Video',
        'lectura' => 'Lectura'
    ], null, [
        'class' => 'form-control custom-select',
        'placeholder' => 'Selecciona una opción',
        'required'
    ]) !!}
</div>

<!-- Categoría Terapéutica -->
<div class="form-group col-sm-6">
    {!! Form::label('categoriaTerapeutica', 'Categoría Terapéutica:') !!}
    {!! Form::text('categoriaTerapeutica', null, [
        'class' => 'form-control',
        'required',
        'placeholder' => 'Relajación, Respiración, Concentración'
    ]) !!}
</div>

<!-- Diagnóstico Dirigido -->
<div class="form-group col-sm-6">
    {!! Form::label('diagnosticoDirigido', 'Diagnóstico Dirigido:') !!}
    {!! Form::text('diagnosticoDirigido', null, [
        'class' => 'form-control',
        'required',
        'placeholder' => 'Ansiedad, Estrés, Depresión leve'
    ]) !!}
</div>

<!-- Nivel de Severidad -->
<div class="form-group col-sm-6">
    {!! Form::label('nivelSeveridad', 'Nivel de Severidad:') !!}
    {!! Form::text('nivelSeveridad', null, [
        'class' => 'form-control',
        'required',
        'placeholder' => 'Leve, Moderado, Severo'
    ]) !!}
</div>

<!-- Tipo de Recurso -->
<div class="form-group col-sm-6">
    {!! Form::label('modo_recurso', 'Tipo de recurso:') !!}
    {!! Form::select('modo_recurso', [
        'archivo' => 'Subir archivo',
        'link' => 'Enlace externo'
    ], null, [
        'class' => 'form-control',
        'id' => 'modo_recurso',
        'placeholder' => 'Selecciona una opción'
    ]) !!}
</div>

<!-- Campo para subir archivo -->
<div id="campo_archivo" class="form-group col-sm-6" style="display:none;">
    {!! Form::label('archivo', 'Archivo (imagen, audio, video o PDF):') !!}
    <div class="input-group">
        <div class="custom-file">
            {!! Form::file('archivo', [
                'class' => 'custom-file-input',
                'id' => 'archivo',
                'accept' => '.pdf,.mp3,.mp4,.avi,.mov,.jpg,.jpeg,.png'
            ]) !!}
            {!! Form::label('archivo', 'Elegir archivo', ['class' => 'custom-file-label']) !!}
        </div>
    </div>
    <small class="form-text text-muted">Formatos permitidos: PDF, MP3, MP4, AVI, MOV, JPG, PNG</small>
</div>

<!-- Campo para enlace externo -->
<div id="campo_link" class="form-group col-sm-6" style="display:none;">
    {!! Form::label('link', 'Enlace del Recurso:') !!}
    {!! Form::text('link', null, [
        'class' => 'form-control',
        'placeholder' => 'Ejemplo: https://www.youtube.com/watch?v=abcd1234'
    ]) !!}
</div>

<div class="clearfix"></div>

<!-- Script para alternar campos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectModo = document.getElementById('modo_recurso');
    const campoArchivo = document.getElementById('campo_archivo');
    const campoLink = document.getElementById('campo_link');

    function toggleCampos() {
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

    selectModo.addEventListener('change', toggleCampos);
    toggleCampos(); // ejecuta al cargar la página por si se recarga el form
});
</script>
