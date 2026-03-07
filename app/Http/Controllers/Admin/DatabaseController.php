<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DatabaseController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Display list of backups
     */
    public function index()
    {
        $backups = [];
        
        if (File::exists($this->backupPath)) {
            $files = File::files($this->backupPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'filename' => $file->getFilename(),
                        'size' => $this->formatFileSize($file->getSize()),
                        'date' => date('Y-m-d H:i:s', $file->getMTime()),
                        'path' => $file->getPathname(),
                    ];
                }
            }
            // Sort by date descending
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return view('admin.database.index', compact('backups'));
    }

    /**
     * Create a new database backup
     */
    public function backup()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;

            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$database}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get create table statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Table structure for `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sql .= "-- Data for table `{$tableName}`\n";
                    
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $values = array_map(function($value) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, $rowArray);
                        
                        $columns = implode('`, `', array_keys($rowArray));
                        $valuesStr = implode(', ', $values);
                        
                        $sql .= "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filepath, $sql);

            return redirect()->route('admin.database.index')
                ->with('success', __('admin.backup_created', ['filename' => $filename]));

        } catch (\Exception $e) {
            return redirect()->route('admin.database.index')
                ->with('error', __('admin.backup_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($filepath)) {
            return redirect()->route('admin.database.index')
                ->with('error', __('admin.file_not_found'));
        }

        return response()->download($filepath);
    }

    /**
     * Delete a backup file
     */
    public function destroy($filename)
    {
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        if (File::exists($filepath)) {
            File::delete($filepath);
            return redirect()->route('admin.database.index')
                ->with('success', __('admin.backup_deleted'));
        }

        return redirect()->route('admin.database.index')
            ->with('error', __('admin.file_not_found'));
    }

    /**
     * Restore database from backup
     */
    public function restore($filename)
    {
        try {
            $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

            if (!File::exists($filepath)) {
                return redirect()->route('admin.database.index')
                    ->with('error', __('admin.file_not_found'));
            }

            $sql = File::get($filepath);

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Split SQL into individual statements
            $statements = array_filter(array_map('trim', explode(";\n", $sql)));

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--') && !str_starts_with($statement, 'SET FOREIGN_KEY_CHECKS')) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        // Skip errors for comments and empty statements
                        continue;
                    }
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Clear caches
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return redirect()->route('admin.database.index')
                ->with('success', __('admin.restore_success'));

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->route('admin.database.index')
                ->with('error', __('admin.restore_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Upload and restore from uploaded file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:51200', // Max 50MB
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.sql';
            $file->move($this->backupPath, $filename);

            return redirect()->route('admin.database.index')
                ->with('success', __('admin.upload_success', ['filename' => $filename]));

        } catch (\Exception $e) {
            return redirect()->route('admin.database.index')
                ->with('error', __('admin.upload_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Format file size
     */
    protected function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
