<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(glob(storage_path('app/backups/*.sql')))
            ->map(fn($f) => [
                'name'     => basename($f),
                'size'     => round(filesize($f) / 1024, 1) . ' KB',
                'created'  => date('d M Y H:i', filemtime($f)),
                'ts'       => filemtime($f),
            ])
            ->sortByDesc('ts')
            ->values();

        return view('system.backup', compact('backups'));
    }

    public function create()
    {
        $dbName   = config('database.connections.mysql.database');
        $dbUser   = config('database.connections.mysql.username');
        $dbPass   = config('database.connections.mysql.password');
        $dbSocket = config('database.connections.mysql.unix_socket', '');
        $filename = 'wms_backup_' . date('Ymd_His') . '.sql';
        $path     = storage_path('app/backups/' . $filename);

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $socketOpt = $dbSocket ? "--socket={$dbSocket}" : '';
        $cmd = "mysqldump {$socketOpt} -u{$dbUser} " . ($dbPass ? "-p{$dbPass}" : '') . " {$dbName} > {$path} 2>&1";
        exec($cmd, $out, $code);

        if ($code !== 0 || !file_exists($path) || filesize($path) < 10) {
            return back()->with('error', 'Backup failed. Please check database credentials.');
        }

        return back()->with('success', "Backup created: {$filename} (" . round(filesize($path)/1024, 1) . " KB)");
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . basename($filename));
        if (!file_exists($path)) abort(404);

        return response()->download($path, $filename, ['Content-Type' => 'application/octet-stream']);
    }

    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . basename($filename));
        if (file_exists($path)) unlink($path);
        return back()->with('success', 'Backup file deleted.');
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|mimes:sql,txt']);

        $file     = $request->file('backup_file');
        $dbName   = config('database.connections.mysql.database');
        $dbUser   = config('database.connections.mysql.username');
        $dbPass   = config('database.connections.mysql.password');
        $dbSocket = config('database.connections.mysql.unix_socket', '');

        $tmpPath  = $file->getRealPath();
        $socketOpt = $dbSocket ? "--socket={$dbSocket}" : '';
        $cmd = "mysql {$socketOpt} -u{$dbUser} " . ($dbPass ? "-p{$dbPass}" : '') . " {$dbName} < {$tmpPath} 2>&1";
        exec($cmd, $out, $code);

        if ($code !== 0) {
            return back()->with('error', 'Restore failed: ' . implode(' ', $out));
        }

        return back()->with('success', 'Database restored successfully from backup.');
    }
}
