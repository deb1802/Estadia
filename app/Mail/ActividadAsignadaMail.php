<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// Si luego quieres colas, descomenta:
// use Illuminate\Contracts\Queue\ShouldQueue;

class ActividadAsignadaMail extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $pacienteNombre,
        public ?string $medicoNombre,
        public string $actividadNombre,
        public ?string $actividadDescripcion,
        public $fechaAsignacion, // Carbon|string
        public $fechaLimite = null, // Carbon|string|null
        public ?string $urlAccion = null
    ) {}

    public function build()
    {
        // Si tienes MAIL_FROM_* en .env, no necesitas ->from().
        // Aun así, forzamos explícitamente el remitente que pediste:
        $fromAddress = config('mail.from.address', 'mindwaremental@gmail.com');
        $fromName    = config('mail.from.name', 'Mindora');

        return $this->from($fromAddress, $fromName)
            ->subject('Nueva Actividad Terapéutica Asignada')
            ->markdown('emails.actividad_asignada', [
                'pacienteNombre'       => $this->pacienteNombre,
                'medicoNombre'         => $this->medicoNombre,
                'actividadNombre'      => $this->actividadNombre,
                'actividadDescripcion' => $this->actividadDescripcion,
                'fechaAsignacion'      => $this->fechaAsignacion,
                'fechaLimite'          => $this->fechaLimite,
                'urlAccion'            => $this->urlAccion ?? url('/paciente/actividades'),
            ]);
    }
}
