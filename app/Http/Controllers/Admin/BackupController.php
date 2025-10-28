<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backups.index');
    }

    /**
     * Genera un .sql y:
     *  - Si es llamada normal (submit), descarga el archivo.
     *  - Si es llamada vía fetch (AJAX), responde el blob y un header "X-Backup-Filename"
     *    para poder mostrar un mensaje en la vista sin recargar.
     */
    public function backup(Request $request)
    {
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = (string) env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD', '');

        $binPath  = rtrim(env('MYSQL_BIN_PATH', ''), DIRECTORY_SEPARATOR);
        $dumpExec = $binPath ? ($binPath . DIRECTORY_SEPARATOR . 'mysqldump') : 'mysqldump';
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false && !str_ends_with($dumpExec, '.exe')) {
            $dumpExec .= '.exe';
        }

        if (!$database || !$username) {
            return back()->with('error', 'Faltan DB_DATABASE o DB_USERNAME en .env');
        }

        $filename = sprintf('backup_%s_%s.sql', $database, now()->format('Y-m-d_H-i-s'));

        // Construir comando mysqldump y capturar STDOUT
        $cmd = [
            $dumpExec,
            '-h', $host,
            '-P', $port,
            '-u', $username,
            '--routines',
            '--events',
            '--triggers',
            '--single-transaction',
            $database,
        ];
        if ($password !== '') {
            $cmd[] = '-p' . $password;
        }

        try {
            $process = new Process($cmd);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $sqlContent = $process->getOutput();

            // Si es petición AJAX (fetch), devolvemos el blob sin forzar descarga de navegador
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response($sqlContent, 200)
                    ->header('Content-Type', 'application/sql')
                    ->header('X-Backup-Filename', $filename);
            }

            // Petición normal: descargar directo
            return response($sqlContent, 200, [
                'Content-Type'        => 'application/sql',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al generar respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Importa un .sql subido por el admin sobre la BD configurada en .env
     * (NO cambia el nombre de la BD).
     */
    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => ['required','file','mimetypes:text/plain,application/sql','max:51200'], // ~50MB
        ],[
            'sql_file.required' => 'Selecciona un archivo .sql',
            'sql_file.file'     => 'Archivo inválido',
            'sql_file.mimetypes'=> 'Debe ser un .sql de texto',
            'sql_file.max'      => 'El archivo es muy grande (máx. 50MB)',
        ]);

        $host     = env('DB_HOST', '127.0.0.1');
        $port     = (string) env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD', '');

        $binPath  = rtrim(env('MYSQL_BIN_PATH', ''), DIRECTORY_SEPARATOR);
        $mysqlExec = $binPath ? ($binPath . DIRECTORY_SEPARATOR . 'mysql') : 'mysql';
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false && !str_ends_with($mysqlExec, '.exe')) {
            $mysqlExec .= '.exe';
        }

        if (!$database || !$username) {
            return back()->with('error', 'Faltan DB_DATABASE o DB_USERNAME en .env');
        }

        // Guardar temporalmente el archivo en storage (no público)
        $tmpPath = $request->file('sql_file')->storeAs(
            'backups/tmp',
            'restore_'.now()->format('Ymd_His').'.sql'
        );
        $absPath = storage_path('app/'.$tmpPath);

        // Comando mysql para importar
        $cmd = [
            $mysqlExec,
            '--default-character-set=utf8mb4',
            '-h', $host,
            '-P', $port,
            '-u', $username,
            $database,
        ];
        if ($password !== '') {
            $cmd[] = '-p' . $password;
        }

        try {
            // Alimentamos STDIN con el contenido del .sql
            $sqlContent = file_get_contents($absPath);
            $process = new Process($cmd);
            $process->setTimeout(600);
            $process->setInput($sqlContent);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            @unlink($absPath);
            return back()->with('success', '¡Restauración completada! La base de datos se importó correctamente.');
        } catch (\Throwable $e) {
            @unlink($absPath);
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }
}
