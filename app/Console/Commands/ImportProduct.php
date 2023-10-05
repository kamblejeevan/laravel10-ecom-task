<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ProductsImport;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from a CSV file';

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
            $import = new ProductsImport();
            $import->import($file);
        } catch (Exception $e) {
            Log::channel('productlog')->info($e);
        }
    }
}
