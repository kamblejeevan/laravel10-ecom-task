<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class ImportUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('The CSV file does not exist.');
            return;
        }
        try {
            $import = new UsersImport();
            $import->import($file);
        } catch (Exception $e) {
            Log::channel('userlog')->info($data);
        }
    }
}
