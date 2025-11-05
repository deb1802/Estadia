<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PDO;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup.index');
    }

    // ✅ Descargar respaldo .SQL (NO modificar — funciona)
    public function backup()
    {
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $host = env('DB_HOST');

        $file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $outputPath = storage_path("app/{$file}");

        $mysqldump = "C:/xampp/mysql/bin/mysqldump.exe";

        $command = "\"{$mysqldump}\" --host=\"{$host}\" --user=\"{$user}\" --password=\"{$pass}\" --default-character-set=utf8mb4 --no-tablespaces \"{$db}\" --result-file=\"{$outputPath}\"";

        exec($command . " 2>&1", $output);

        return response()->download($outputPath);
    }

    // ✅ Restauración funcional (SOLUCIONADO)
    // En App\Http\Controllers\Admin\BackupController.php
public function restore(Request $request)
{
    \Log::info('[RESTORE] Inicio');

    if (!$request->hasFile('backup_file')) {
        \Log::error('[RESTORE] No llegó archivo backup_file');
        return back()->with('error', 'No llegó archivo (name="backup_file"). Revisa el input y el enctype="multipart/form-data".');
    }

    $file = $request->file('backup_file');

    if (!$file->isValid()) {
        \Log::error('[RESTORE] Archivo inválido. Error code: '.$file->getError());
        return back()->with('error', 'Archivo inválido. Código: '.$file->getError());
    }

    // Guarda una copia para inspeccionar el archivo que realmente llega a Laravel
    $storedPath = $file->storeAs('backups_uploads', 'restore_'.date('Ymd_His').'.sql');
    \Log::info('[RESTORE] Archivo subido como: '.$storedPath.' (size='.$file->getSize().' bytes)');

    $sqlPath = storage_path('app/'.$storedPath);

    try {
        // Desactivar FKs y borrar tablas
        $pdo = \DB::connection()->getPdo();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        $dbName = env('DB_DATABASE');
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $t) {
            $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
        }
        \Log::info('[RESTORE] Tablas eliminadas: '.implode(',', $tables));

        // Leer SQL
        $sql = file_get_contents($sqlPath);
        \Log::info('[RESTORE] Bytes leídos del SQL: '.strlen($sql));

        // Limpieza ligera de comentarios que a veces frenan
        $sql = preg_replace('/\/\*![0-9]+.*?\*\//s', '', $sql);
        // Ejecutar por bloques (para ver si alguna sentencia truena)
        $stmts = preg_split('/;\s*[\r\n]+/', $sql);
        $ok = 0; $fail = 0; $firstError = null;

        foreach ($stmts as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') continue;

            try {
                $pdo->exec($stmt);
                $ok++;
            } catch (\Throwable $e) {
                $fail++;
                if ($firstError === null) {
                    $firstError = $e->getMessage()." | STMT: ".substr($stmt, 0, 300).'...';
                }
                // Loguea pero sigue con las demás para identificar patrón
                \Log::error('[RESTORE] Error stmt: '.$e->getMessage());
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        \Log::info("[RESTORE] Fin. OK={$ok}, FAIL={$fail}");

        if ($fail > 0) {
            return back()->with('error', "Se importó parcialmente. OK={$ok}, FAIL={$fail}. Primer error: ".$firstError);
        }

        return back()->with('success', "✅ Restauración completa. Sentencias ejecutadas: {$ok}");

    } catch (\Throwable $e) {
        \Log::error('[RESTORE] EXCEPTION: '.$e->getMessage());
        return back()->with('error', 'Excepción al restaurar: '.$e->getMessage());
    }
}

}
