<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue; // si luego lo encolas

class TestAsignadoMail extends Mailable // implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param string $pacienteNombre
     * @param string|null $medicoNombre
     * @param array $testsAsignados  // [['nombre'=>'GAD-7','tipo'=>'Ansiedad'], ...]
     * @param \Carbon\Carbon|string $fechaAsignacion
     * @param string|null $urlAccion
     */
    public function __construct(
        public string $pacienteNombre,
        public ?string $medicoNombre,
        public array $testsAsignados,
        public $fechaAsignacion,
        public ?string $urlAccion = null
    ) {}

    public function build()
    {
        $fromAddress = config('mail.from.address', 'mindwaremental@gmail.com');
        $fromName    = config('mail.from.name', 'Mindora');

        return $this->from($fromAddress, $fromName)
            ->subject('Nuevo(s) test(s) psicológicos asignados')
            ->markdown('emails.tests_asignados', [
                'pacienteNombre'  => $this->pacienteNombre,
                'medicoNombre'    => $this->medicoNombre,
                'testsAsignados'  => $this->testsAsignados,
                'fechaAsignacion' => $this->fechaAsignacion,
                'urlAccion'       => $this->urlAccion ?? url('/paciente/tests'),
            ]);
    }
}
