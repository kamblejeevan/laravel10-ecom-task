<?php

namespace App\Imports;

use App\Models\User;
use Error;
use Illuminate\Support\Facades\Hash;
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

class UsersImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnError, SkipsOnFailure
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
         Log::channel('userlog')->info($log_data);
     }

    public function rules(): array
    {
        return [
            'firstName' => 'required',
            'email'     => 'required|email|unique:users,email'
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        return new User([
            'first_name'        => $row['firstName'],
            'last_name'         => $row['lastName'],
            'phone_no'          => $row['phoneNumber'],
            'email'             => $row['email'],
            'password'          => bcrypt('password')
        ]);
    }
}
