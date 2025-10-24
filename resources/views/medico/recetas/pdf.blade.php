@php
  $title = 'Receta médica #'.$receta->idReceta;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
  /* === Página y tipografía === */
  @page { margin: 18mm 16mm 20mm 16mm; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; } /* slate-900 */
  h1,h2,h3,h4 { margin: 0; }
  small { color:#64748b; } /* slate-500 */

  /* === Utilidades === */
  .row { display:flex; flex-wrap:wrap; margin:0 -8px; }
  .col { padding:0 8px; }
  .col-3{ width:25%; } .col-4{ width:33.333%; } .col-6{ width:50%; } .col-12{ width:100%; }
  .muted{ color:#64748b; }
  .mb-2{ margin-bottom:8px; } .mb-3{ margin-bottom:12px; } .mb-4{ margin-bottom:16px; }
  .mt-2{ margin-top:8px; } .mt-3{ margin-top:12px; } .mt-4{ margin-top:16px; }
  .badge { display:inline-block; background:#e2e8f0; color:#0f172a; border-radius:999px; padding:2px 10px; font-size:11px; }
  .chip { display:inline-block; background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:600; }
  .divider { height:1px; background:#e5e7eb; margin:10px 0 14px; }

  /* === Header profesional === */
  .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 14px; }
  .logo { height: 36px; }
  .brand { font-size: 18px; font-weight: 800; color:#1d4ed8; letter-spacing:.3px; }
  .folio { font-weight:700; }

  /* === Tarjetas (cajas) === */
  .box { border:1px solid #e5e7eb; border-radius:10px; padding:12px; margin-bottom:12px; background:#ffffff; }
  .box-title { font-weight:700; color:#0f172a; margin-bottom:8px; }
  .kv { font-size:12px; line-height:1.35; }
  .kv strong { display:inline-block; min-width:90px; }

  /* === Tabla de medicamentos === */
  table { width:100%; border-collapse:collapse; }
  thead th {
    background:#f1f5f9; /* slate-100 */
    border-bottom:2px solid #e2e8f0;
    padding:8px 8px; text-align:left; font-size:12px;
  }
  tbody td {
    border-bottom:1px solid #e5e7eb;
    padding:8px 8px; vertical-align:top;
  }
  tbody tr:nth-child(odd) td { background:#fafafa; } /* zebra suave */
  .t-col-med { width:32%; } .t-col-pres{ width:18%; } .t-col-dos{ width:18%; } .t-col-frec{ width:16%; } .t-col-dur{ width:16%; }

  /* === Footer === */
  .footer { position: fixed; bottom: 10mm; left: 16mm; right: 16mm; color:#64748b; font-size: 11px; }
  .footer .line { height:1px; background:#e5e7eb; margin-bottom:6px; }
</style>
</head>
<body>

  {{-- Encabezado --}}
  <div class="header">
    <div class="row" style="align-items:center;">
      <div class="col col-6" style="display:flex; align-items:center; gap:10px;">
        <img class="logo" src="{{ public_path('img/logo.png') }}" alt="Mindware">
        <div>
          <div class="brand">Receta médica</div>
          <div class="muted" style="font-size:11px;">Sistema Mindware</div>
        </div>
      </div>
      <div class="col col-6" style="text-align:right;">
        <div class="folio">Folio: #{{ $receta->idReceta }}</div>
        <div><strong>Fecha receta:</strong> {{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}</div>
        <div class="muted">Generado: {{ $hoy }}</div>
      </div>
    </div>
  </div>

  {{-- Bloques principales --}}
  <div class="row">
    <div class="col col-6">
      <div class="box">
        <div class="box-title">Paciente</div>
        <div class="kv"><strong>Nombre:</strong> {{ $receta->paciente_nombre }} {{ $receta->paciente_apellido }}</div>
        <div class="mt-2"><span class="chip">Receta vigente</span></div>
      </div>
    </div>
    <div class="col col-6">
      <div class="box">
        <div class="box-title">Médico</div>
        <div class="kv"><strong>Nombre:</strong> {{ $receta->medico_nombre }} {{ $receta->medico_apellido }}</div>
        @if($receta->especialidad)
          <div class="kv"><strong>Especialidad:</strong> {{ $receta->especialidad }}</div>
        @endif
        @if($receta->cedulaProfesional)
          <div class="kv"><strong>Cédula:</strong> {{ $receta->cedulaProfesional }}</div>
        @endif
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-title">Observaciones</div>
    <div class="muted" style="line-height:1.5;">
      {{ $receta->observaciones ?: 'Sin observaciones' }}
    </div>
  </div>

  <div class="box">
    <div class="box-title">Medicamentos prescritos</div>
    <table>
      <thead>
        <tr>
          <th class="t-col-med">Medicamento</th>
          <th class="t-col-pres">Presentación</th>
          <th class="t-col-dos">Dosis</th>
          <th class="t-col-frec">Frecuencia</th>
          <th class="t-col-dur">Duración</th>
        </tr>
      </thead>
      <tbody>
        @forelse($detalles as $d)
          <tr>
            <td>{{ $d->nombre }}</td>
            <td>{{ $d->presentacion }}</td>
            <td>{{ $d->dosis }}</td>
            <td>{{ $d->frecuencia }}</td>
            <td>{{ $d->duracion }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted">Sin medicamentos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer con número de página --}}
  <div class="footer">
    <div class="line"></div>
    <div class="row">
      <div class="col col-6">
        <span class="muted">Documento emitido por Mindware</span>
      </div>
      <div class="col col-6" style="text-align:right;">
        <span class="muted">Página <span class="pagenum"></span> de <span class="pagecount"></span></span>
      </div>
    </div>
  </div>

  {{-- Page numbers para DomPDF --}}
  <script type="text/php">
    if (isset($pdf)) {
      $x = 520; $y = 780; // coordenadas aprox para Letter; DomPDF ajusta
      $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
      $font = $fontMetrics->get_font("DejaVu Sans", "normal");
      $size = 10;
      $pdf->page_text($x, $y, $text, $font, $size, array(100/255,116/255,139/255)); // slate-500
    }
  </script>

</body>
</html>
