@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f; --ink-2:#2c4c86; --sky:#eaf3ff; --card:#ffffff; --stroke:#e6eefc; --ok:#1e9e6e; --warn:#e39b17; --danger:#d9534f;
  }
  .wrap{
    min-height: calc(100vh - var(--navbar-h,56px));
    background: radial-gradient(1000px 600px at 10% -10%, #eef6ff 0%, #f6fbff 60%, #fff 100%);
    padding: 28px 14px 42px;
  }
  .page-title{ color: var(--ink); font-weight: 800; letter-spacing: .2px; }
  .subtext{ opacity:.85 }
  .card-lite{
    border: 1px solid var(--stroke);
    border-radius: 16px;
    background: var(--card);
    box-shadow: 0 6px 16px rgba(27,59,111,.08);
  }
  .btn-soft{ border-radius: 14px; padding: 12px 18px; font-weight: 700; }
  .btn-backup{
    background: linear-gradient(180deg, #dff3ff, #eaf7ff);
    border: 1px solid #cfe8ff;
    color: #0b5ed7;
  }
  .btn-restore{
    background: linear-gradient(180deg, #e9fff6, #f3fffb);
    border: 1px solid #cdeede;
    color: #0a7f57;
  }
  .divider{ height:1px; background: var(--stroke); }
  .badge-soft{
    background:#eef6ff; color:#1b3b6f; border:1px solid #d8e7ff; border-radius:999px; padding:.35rem .7rem; font-weight:700;
  }
  .alert-fixed{
    position: fixed; right: 16px; bottom: 16px; z-index: 1060; min-width: 320px;
    box-shadow: 0 10px 24px rgba(0,0,0,.12);
  }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="container-fluid px-3 px-lg-2">
    <div class="row justify-content-center mb-4">
      <div class="col-12 col-xl-10">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
          <div>
            <h1 class="page-title mb-1"><i class="bi bi-hdd-network me-2"></i> Respaldos y Restauración</h1>
            <p class="mb-0 subtext">Genera un archivo <strong>.sql</strong> para guardar en tu equipo o restaura desde uno que tengas.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-soft"><i class="bi bi-shield-lock me-1"></i> Solo Administrador</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Mensajes flash (restore) --}}
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
          <div class="alert alert-info">{{ session('info') }}</div>
        @endif
      </div>
    </div>

    <div class="row g-4 justify-content-center">
      <div class="col-12 col-xl-5">
        <div class="card-lite p-4 h-100">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="fs-2"><i class="bi bi-cloud-arrow-down-fill"></i></div>
            <div>
              <h4 class="mb-1">Respaldar base de datos</h4>
              <div class="hint">Se generará un <strong>.sql</strong> y se descargará a tu computadora.</div>
            </div>
          </div>

          <div class="divider my-3"></div>

          {{-- Botón via fetch para descargar y mostrar aviso --}}
          <form id="backupForm" method="POST" action="{{ url('/admin/backups/backup') }}">
            @csrf
            <button id="backupBtn" type="submit" class="btn btn-soft btn-backup w-100">
              <i class="bi bi-download me-2"></i> Generar y descargar respaldo
            </button>
          </form>

          <p class="mt-3 hint mb-0"><i class="bi bi-info-circle me-1"></i> El nombre incluirá la base actual y fecha, ej. <code>backup_{{ env('DB_DATABASE') }}_YYYY-MM-DD_HH-mm-ss.sql</code></p>
        </div>
      </div>

      <div class="col-12 col-xl-5">
        <div class="card-lite p-4 h-100">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="fs-2"><i class="bi bi-cloud-arrow-up-fill"></i></div>
            <div>
              <h4 class="mb-1">Restaurar desde archivo</h4>
              <div class="hint">Selecciona un <strong>.sql</strong> de tu equipo para restaurar la base actual.</div>
            </div>
          </div>

          <div class="divider my-3"></div>

          <form method="POST" action="{{ url('/admin/backups/restore') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="sqlFile" class="form-label fw-semibold">Archivo .sql</label>
              <input class="form-control" type="file" id="sqlFile" name="sql_file" accept=".sql" required>
              <div class="form-text">Se importará en la base configurada en tu <code>.env</code>.</div>
            </div>

            <button type="submit" class="btn btn-soft btn-restore w-100">
              <i class="bi bi-upload me-2"></i> Restaurar
            </button>
          </form>

          <p class="mt-3 hint mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Recomendación: prueba primero en una copia antes de producción.</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Aviso flotante --}}
<div id="toastBox" class="alert alert-success alert-fixed d-none" role="alert">
  <div class="d-flex align-items-start">
    <i class="bi bi-check-circle-fill me-2 mt-1"></i>
    <div>
      <strong>¡Respaldo descargado!</strong>
      <div id="toastMsg" class="mt-1 small"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const form   = document.getElementById('backupForm');
  const btn    = document.getElementById('backupBtn');
  const toast  = document.getElementById('toastBox');
  const toastMsg = document.getElementById('toastMsg');

  // Toma CSRF desde meta si tu layout lo tiene; si no, lo leemos del input hidden
  function getCsrf(){
    const meta = document.querySelector('meta[name="csrf-token"]');
    if(meta) return meta.getAttribute('content');
    const inp = form.querySelector('input[name="_token"]');
    return inp ? inp.value : '';
  }

  form.addEventListener('submit', async function(ev){
    ev.preventDefault();
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Generando…';

    try{
      const resp = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': getCsrf(),
          'Accept': 'application/octet-stream'
        },
        body: new FormData(form)
      });

      if(!resp.ok){
        throw new Error('Falló la generación del respaldo ('+resp.status+')');
      }

      // Leemos el nombre del archivo desde el header personalizado
      const filename = resp.headers.get('X-Backup-Filename') || 'backup.sql';
      const blob = await resp.blob();

      // Disparar descarga en el navegador
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);

      // Mostrar aviso bonito con el nombre a buscar
      showToast('El archivo se descargó como <code>'+filename+'</code>. Búscalo en tu carpeta de descargas.');

    }catch(err){
      showToast('No se pudo completar el respaldo: '+ err.message, true);
    }finally{
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-download me-2"></i> Generar y descargar respaldo';
    }
  });

  function showToast(html, isError){
    toast.classList.remove('d-none', 'alert-success', 'alert-danger');
    toast.classList.add(isError ? 'alert-danger' : 'alert-success');
    toastMsg.innerHTML = html;
    // Mostrar
    toast.style.opacity = 1;
    // Ocultar después de 6s
    setTimeout(()=>{ 
      toast.style.opacity = 0; 
      setTimeout(()=> toast.classList.add('d-none'), 350);
    }, 6000);
  }
})();
</script>
@endpush
