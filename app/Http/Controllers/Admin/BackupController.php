<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /** Vista principal de respaldos */
    public function index()
    {
        return view('admin.backups.index');
    }

    /**
     * Genera un respaldo .sql y lo descarga (o lo devuelve como blob si es AJAX).
     * En Windows evitamos TCP y 'localhost' usando Named Pipe:
     *  1) -h .     (punto)
     *  2) --pipe
     *  3) --protocol=PIPE
     *  4) 127.0.0.1 (último recurso)
     */
    public function backup(Request $request)
    {
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = (string) env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD', '');

        $binPath  = rtrim((string) env('MYSQL_BIN_PATH', ''), DIRECTORY_SEPARATOR);
        $dumpExec = $binPath ? ($binPath . DIRECTORY_SEPARATOR . 'mysqldump') : 'mysqldump';
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false && !str_ends_with($dumpExec, '.exe')) {
            $dumpExec .= '.exe';
        }

        if (!$database || !$username) {
            return $this->backupErrorResponse($request, 'Faltan DB_DATABASE o DB_USERNAME en el .env');
        }
        if ($binPath && stripos(PHP_OS_FAMILY, 'Windows') !== false && !file_exists($dumpExec)) {
            return $this->backupErrorResponse($request, 'No se encontró mysqldump en "'.$dumpExec.'". Verifica MYSQL_BIN_PATH en tu .env');
        }

        $filename = sprintf('backup_%s_%s.sql', $database, now()->format('Y-m-d_H-i-s'));

        // ===== Intentos en cascada (Windows primero evita TCP/localhost) =====
        $attempts = [];
        if (stripos(PHP_OS_FAMILY,'Windows') !== false) {
            // 1) Named Pipe por host punto (.)
            $attempts[] = [
                $dumpExec, '-h', '.', '-u', $username,
                '--routines','--events','--triggers','--single-transaction', $database
            ];
            // 2) Named Pipe (MariaDB)
            $attempts[] = [
                $dumpExec, '--pipe', '-u', $username,
                '--routines','--events','--triggers','--single-transaction', $database
            ];
            // 3) Named Pipe (MySQL)
            $attempts[] = [
                $dumpExec, '--protocol=PIPE', '-u', $username,
                '--routines','--events','--triggers','--single-transaction', $database
            ];
            // 4) TCP 127.0.0.1 (último recurso)
            $attempts[] = [
                $dumpExec, '-h', '127.0.0.1', '-P', $port, '-u', $username,
                '--routines','--events','--triggers','--single-transaction', $database
            ];
        } else {
            // Unix-like: TCP normal
            $attempts[] = [
                $dumpExec, '-h', $host, '-P', $port, '-u', $username,
                '--routines','--events','--triggers','--single-transaction', $database
            ];
        }

        if ($password !== '') {
            foreach ($attempts as &$a) { $a[] = '-p'.$password; }
            unset($a);
        }

        $lastErr = '';
        foreach ($attempts as $cmd) {
            try {
                $p = new Process($cmd);
                $p->setTimeout(300);
                $p->run();

                if ($p->isSuccessful()) {
                    $sqlContent = $p->getOutput();

                    // AJAX: blob + nombre sugerido en header
                    if ($request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response($sqlContent, 200)
                            ->header('Content-Type', 'application/sql')
                            ->header('X-Backup-Filename', $filename);
                    }

                    // No-AJAX: descarga directa
                    return response($sqlContent, 200, [
                        'Content-Type'        => 'application/sql',
                        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                    ]);
                } else {
                    $lastErr = trim($p->getErrorOutput());
                }
            } catch (\Throwable $e) {
                $lastErr = $e->getMessage();
            }
        }

        return $this->backupErrorResponse($request, "No se pudo generar el respaldo.\nDetalles: ".$lastErr);
    }

    /**
     * Restaura un .sql subido por el admin sobre la BD del .env (no cambia el nombre de la BD).
     * En Windows usa el mismo orden de intentos que backup().
     */
    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => ['required','file','mimetypes:text/plain,application/sql','max:51200'],
        ],[
            'sql_file.required' => 'Selecciona un archivo .sql',
            'sql_file.mimetypes'=> 'Debe ser un .sql de texto',
            'sql_file.max'      => 'El archivo es muy grande (máx. 50 MB)',
        ]);

        $host     = env('DB_HOST', '127.0.0.1');
        $port     = (string) env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD', '');

        $binPath   = rtrim((string) env('MYSQL_BIN_PATH', ''), DIRECTORY_SEPARATOR);
        $mysqlExec = $binPath ? ($binPath . DIRECTORY_SEPARATOR . 'mysql') : 'mysql';
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false && !str_ends_with($mysqlExec, '.exe')) {
            $mysqlExec .= '.exe';
        }

        if (!$database || !$username) {
            return back()->with('error', 'Faltan DB_DATABASE o DB_USERNAME en el .env');
        }
        if ($binPath && stripos(PHP_OS_FAMILY, 'Windows') !== false && !file_exists($mysqlExec)) {
            return back()->with('error', 'No se encontró mysql en "'.$mysqlExec.'". Verifica MYSQL_BIN_PATH en tu .env');
        }

        // Guardar temporal el .sql
        $tmpPath  = $request->file('sql_file')->storeAs('backups/tmp', 'restore_'.now()->format('Ymd_His').'.sql');
        $absPath  = storage_path('app/'.$tmpPath);
        $sqlInput = file_get_contents($absPath);

        $attempts = [];
        if (stripos(PHP_OS_FAMILY,'Windows') !== false) {
            // 1) Named Pipe por host punto (.)
            $attempts[] = [
                $mysqlExec, '--default-character-set=utf8mb4',
                '-h', '.', '-u', $username, $database
            ];
            // 2) Named Pipe (MariaDB)
            $attempts[] = [
                $mysqlExec, '--default-character-set=utf8mb4',
                '--pipe', '-u', $username, $database
            ];
            // 3) Named Pipe (MySQL)
            $attempts[] = [
                $mysqlExec, '--default-character-set=utf8mb4',
                '--protocol=PIPE', '-u', $username, $database
            ];
            // 4) TCP 127.0.0.1
            $attempts[] = [
                $mysqlExec, '--default-character-set=utf8mb4',
                '-h', '127.0.0.1', '-P', $port, '-u', $username, $database
            ];
        } else {
            // Unix-like
            $attempts[] = [
                $mysqlExec, '--default-character-set=utf8mb4',
                '-h', $host, '-P', $port, '-u', $username, $database
            ];
        }

        if ($password !== '') {
            foreach ($attempts as &$a) { $a[] = '-p'.$password; }
            unset($a);
        }

        try {
            $lastErr = '';
            foreach ($attempts as $cmd) {
                $p = new Process($cmd);
                $p->setTimeout(600);
                $p->setInput($sqlInput);
                $p->run();

                if ($p->isSuccessful()) {
                    @unlink($absPath);
                    return back()->with('success', '¡Restauración completada! La base de datos se importó correctamente.');
                } else {
                    $lastErr = trim($p->getErrorOutput());
                }
            }

            @unlink($absPath);
            return back()->with('error', "No se pudo restaurar.\nDetalles: ".$lastErr);
        } catch (\Throwable $e) {
            @unlink($absPath);
            return back()->with('error', 'Error al restaurar: '.$e->getMessage());
        }
    }

    /** Diagnóstico de binarios y versiones (JSON). */
    public function diag()
    {
        $bin = rtrim((string) env('MYSQL_BIN_PATH',''), DIRECTORY_SEPARATOR);
        $dump = $bin ? $bin.DIRECTORY_SEPARATOR.'mysqldump' : 'mysqldump';
        $cli  = $bin ? $bin.DIRECTORY_SEPARATOR.'mysql'     : 'mysql';

        if (stripos(PHP_OS_FAMILY,'Windows') !== false) {
            if (!str_ends_with($dump,'.exe')) $dump .= '.exe';
            if (!str_ends_with($cli ,'.exe')) $cli  .= '.exe';
        }

        $out = [
            'PHP_OS_FAMILY'   => PHP_OS_FAMILY,
            'MYSQL_BIN_PATH'  => env('MYSQL_BIN_PATH'),
            'DB_HOST'         => env('DB_HOST'),
            'DB_PORT'         => env('DB_PORT'),
            'DB_DATABASE'     => env('DB_DATABASE'),
            'DB_USERNAME'     => env('DB_USERNAME'),
            'exists_mysqldump'=> file_exists($dump),
            'exists_mysql'    => file_exists($cli),
        ];

        $v1 = new Process([$dump, '--version']); $v1->run();
        $out['mysqldump_version_ok'] = $v1->isSuccessful();
        $out['mysqldump_version']    = $v1->getOutput() ?: $v1->getErrorOutput();

        $v2 = new Process([$cli, '--version']);  $v2->run();
        $out['mysql_version_ok'] = $v2->isSuccessful();
        $out['mysql_version']    = $v2->getOutput() ?: $v2->getErrorOutput();

        return response()->json($out);
    }

    /** Helper para errores (JSON 422 en AJAX; flash en no-AJAX). */
    private function backupErrorResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['error' => $message], 422);
        }
        return back()->with('error', $message);
    }
}
