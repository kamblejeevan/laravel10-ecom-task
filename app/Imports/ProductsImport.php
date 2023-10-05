<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Log;

HeadingRowFormatter::default('none');

class ProductsImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

     public function onFailure(Failure ...$failures)
     {
         foreach ($failures as $failure) {
             $failureData[] = [
                 'row' => $failure->row(),
                 'attribute' => $failure->attribute(),
                 'values' => json_encode($failure->values()),
                 'errors' => json_encode($failure->errors()),
             ];
         }
         $log_data = json_encode($failureData);
         Log::channel('productlog')->info($log_data);
     }

    public function rules(): array
    {
        return [
            'productname' => 'required',
            'price' => 'required|numeric'
        ];
    }
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        return new Product([
            'name'     => $row['productname'],
            'price'    => $row['price'],
        ]);
    }
}
