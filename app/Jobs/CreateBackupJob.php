<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 4;
    public $timeout = 600;
    protected $source;

    public function __construct(string $source = null)
    {
        if ($source) {
            $this->source = $source;
        } else {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller = $trace[1]['class'] ?? 'desconocido';
            $this->source = $caller;
        }
    }

    public function middleware()
    {
        return [
            // Usa una clave fija y expireAfter corto (por ejemplo, 30 segundos)
            (new WithoutOverlapping('create-backup-job'))->expireAfter(30),
        ];
    }


    public function handle()
    {
        if (Cache::get('skip_next_backup')) {
            Log::info("🛑 Se canceló CreateBackupJob porque se acaba de hacer una restauración.");
            Cache::forget('skip_next_backup');
            return;
        }

        $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
        ->map(function ($b) {
            return ($b['class'] ?? '') . '::' . ($b['function'] ?? '') . ' — ' . ($b['file'] ?? '') . ':' . ($b['line'] ?? '');
        })->toArray();

        \Log::info("🚀 Iniciando CreateBackupJob desde: {$this->source}", [
            'trace' => $trace,
        ]);


        $timestamp = now()->format('Ymd_His');
        $folder = 'backup-temp';

        if (!Storage::disk('local')->exists($folder)) {
            Storage::disk('local')->makeDirectory($folder);
        }

        $fileName = "backup_{$timestamp}.sql";
        $filePath = storage_path("app/{$folder}/{$fileName}");

        $password = env('DB_PASSWORD');
        $user     = env('DB_USERNAME');
        $host     = env('DB_HOST', 'localhost');
        $dbName   = env('DB_DATABASE');
        $port     = env('DB_PORT', 5432);
        $pgDump   = env('PG_DUMP_PATH', 'pg_dump');

        putenv("PGPASSWORD={$password}");

        $command = sprintf(
            '%s -U %s -h %s -p %s -F c --no-owner --no-acl --exclude-table=sessions -b -v -f %s %s 2>&1',
            escapeshellarg($pgDump),
            escapeshellarg($user),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($filePath),
            escapeshellarg($dbName)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $error = implode("\n", $output);

            Log::error("❌ pg_dump falló con el siguiente error:\n{$error}");
            Log::debug("🧪 Comando ejecutado: {$command}");
            Log::debug("📁 Archivo esperado: {$filePath}");

            Cache::put("backup_error_{$timestamp}", $error, now()->addMinutes(10));

            throw new \Exception("pg_dump falló: {$error}");
        }

        Log::info("✅ Backup creado exitosamente: {$fileName}");
    }

    public function failed(\Throwable $exception)
    {
        \Log::critical('🚨 CreateBackupJob ha fallado', [
            'source' => $this->source,
            'error' => $exception->getMessage(),
        ]);
    }
}
