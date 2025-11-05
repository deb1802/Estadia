<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CitaCanceladaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Datos que se envían al correo.
     */
    public function __construct(
        public string $medicoNombre,
        public string $pacienteNombre,
        public string $fechaCita,       // Fecha y hora de la cita
        public ?string $motivo = null,  // Motivo original de la cita
        public ?string $ubicacion = null,
        public ?string $canceladaPor = null, // Puede ser 'paciente', 'medico' o 'admin'
        public ?string $urlAccion = null
    ) {}

    /**
     * Construir el mensaje del correo.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'mindwaremental@gmail.com');
        $fromName    = config('mail.from.name', 'Mindware');

        // Determinar destinatario y mensaje según quién canceló
        $subject = match ($this->canceladaPor) {
            'paciente' => 'Cita cancelada por el paciente',
            'medico'   => 'Cita cancelada por el médico',
            'admin'    => 'Cita eliminada por el administrador',
            default    => 'Cita cancelada',
        };

        // Ruta de acción según tipo de usuario
        $url = $this->urlAccion ?? match ($this->canceladaPor) {
            'paciente' => url('/medico/citas'),
            'medico'   => url('/paciente/citas'),
            'admin'    => url('/paciente/citas'),
            default    => url('/'),
        };

        return $this->from($fromAddress, $fromName)
            ->subject($subject)
            ->markdown('emails.cita_cancelada', [
                'medicoNombre'   => $this->medicoNombre,
                'pacienteNombre' => $this->pacienteNombre,
                'fechaCita'      => $this->fechaCita,
                'motivo'         => $this->motivo,
                'ubicacion'      => $this->ubicacion,
                'canceladaPor'   => $this->canceladaPor,
                'urlAccion'      => $url,
            ]);
    }
}
