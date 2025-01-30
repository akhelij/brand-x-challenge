<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

// I love this package, I have used it many times to manage imports in laravel application.
// It is very easy to use, keep the code clean and maintainable.
// What I love the most about it is that it help us queue the failures also !
class EmployeesImport implements
    ToModel,
    WithChunkReading,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    SkipsOnError,
    SkipsOnFailure,
    ShouldQueue
{
    public function model(array $row)
    {
        return new Employee([
            'import_reference' => (string) $row['emp_id'],
            'username' => $row['user_name'],
            'name_prefix' => $row['name_prefix'],
            'first_name' => $row['first_name'],
            'middle_initial' => $row['middle_initial'],
            'last_name' => $row['last_name'],
            'gender' => $row['gender'],
            'email' => $row['e_mail'],
            'date_of_birth' => \Carbon\Carbon::createFromFormat('n/j/Y', $row['date_of_birth'])->format('Y-m-d'),
            'time_of_birth' => $row['time_of_birth'],
            'age_in_years' => $row['age_in_yrs'],
            'date_of_joining' => \Carbon\Carbon::createFromFormat('n/j/Y', $row['date_of_joining'])->format('Y-m-d'),
            'age_in_company_years' => $row['age_in_company_years'],
            'phone_number' => $row['phone_no'],
            'place_name' => $row['place_name'],
            'county' => $row['county'],
            'city' => $row['city'],
            'zip' => (string) $row['zip'],
            'region' => $row['region'],
        ]);
    }

    public function rules(): array
    {
        return [
            'emp_id' => ['required'],
            'user_name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'e_mail' => ['required', 'email'],
            'gender' => ['required', 'in:M,F'],
            'date_of_birth' => ['required'],
            'date_of_joining' => ['required'],
            'zip' => ['required'],
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function onError(Throwable $e)
    {
        Log::error('Employee Import Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Employee Import Validation Failure', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }
}
