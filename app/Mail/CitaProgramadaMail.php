<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue;

class CitaProgramadaMail extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $pacienteNombre,
        public ?string $medicoNombre,
        public string $fechaHora,          // ✅ cambiado de fechaCita → fechaHora
        public ?string $motivo = null,
        public ?string $ubicacion = null,
        public ?string $urlAccion = null
    ) {}

    public function build()
    {
        $fromAddress = config('mail.from.address', 'mindwaremental@gmail.com');
        $fromName    = config('mail.from.name', 'Mindware');

        return $this->from($fromAddress, $fromName)
            ->subject('Nueva cita programada')
            ->markdown('emails.cita_programada', [
                'pacienteNombre' => $this->pacienteNombre,
                'medicoNombre'   => $this->medicoNombre,
                'fechaHora'      => $this->fechaHora, // ✅ ahora existe
                'motivo'         => $this->motivo,
                'ubicacion'      => $this->ubicacion,
                'urlAccion'      => $this->urlAccion ?? url('/paciente/citas'),
            ]);
    }
}
