<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Foundation\Bus\Dispatchable;

class RestoreBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 4;
    public $timeout = 600;
    protected $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function middleware()
    {
        // Usar un lock único basado en el nombre del archivo para evitar solapamientos en restores
        return [(new WithoutOverlapping('restore_' . md5($this->fileName)))->expireAfter(600)];
    }

    public function handle()
    {
        $folder = 'backup-temp';
        $filePath = storage_path("app/{$folder}/{$this->fileName}");

        if (!file_exists($filePath)) {
            Log::error("❌ Archivo de backup no encontrado: {$this->fileName}");
            // Mejor lanzar excepción para que el job falle y se reintente o se registre correctamente
            throw new \Exception("Archivo de backup no encontrado: {$this->fileName}");
        }

        Cache::put('restauracion_activa', true, now()->addMinutes(30));

        try {
            Log::info("🚀 Iniciando restauración de backup: {$this->fileName}");

            Artisan::call('down');

            $password = env('DB_PASSWORD');
            $user     = env('DB_USERNAME');
            $host     = env('DB_HOST', 'localhost');
            $dbName   = env('DB_DATABASE');
            $port     = env('DB_PORT', 5432);
            $pgRestore = env('PG_RESTORE_PATH', 'pg_restore');

            putenv("PGPASSWORD={$password}");

            $command = sprintf(
                '%s -U %s -h %s -p %s -d %s --clean --no-owner --no-acl --verbose %s 2>&1',
                escapeshellarg($pgRestore),
                escapeshellarg($user),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error('❌ Error al restaurar backup', [
                    'archivo' => $this->fileName,
                    'output' => $output,
                    'exit_code' => $returnVar
                ]);
                throw new \Exception('pg_restore falló: ' . implode("\n", $output));
            }

            // Reasignar permisos tras restore
            DB::statement("ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO {$user}");
            DB::statement("GRANT CONNECT ON DATABASE {$dbName} TO {$user}");
            DB::statement("GRANT USAGE ON SCHEMA public TO {$user}");
            DB::statement("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO {$user}");
            DB::statement("GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO {$user}");
            DB::statement("GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO {$user}");

            Log::info('✅ Backup restaurado exitosamente', ['archivo' => $this->fileName]);
        } finally {
            try {
                if (DB::getSchemaBuilder()->hasTable('sessions')) {
                    DB::table('sessions')->truncate();
                }
            } catch (\Throwable $e) {
                Log::warning('No se pudo truncar tabla sessions', ['error' => $e->getMessage()]);
            }

            Artisan::call('up');
            Cache::put('skip_next_backup', true, now()->addMinutes(2));
            Cache::forget('restauracion_activa');
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical('🚨 RestoreBackupJob ha fallado', [
            'archivo' => $this->fileName,
            'error' => $exception->getMessage(),
        ]);
    }
}
