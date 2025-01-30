<?php

namespace App\Strategies;

use App\Contracts\ImportStrategy;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;

class CsvImportStrategy implements ImportStrategy
{
    public function __construct(private string $filePath) {}

    public function import(): array
    {
        Excel::queueImport(new EmployeesImport, $this->filePath, null, \Maatwebsite\Excel\Excel::CSV);

        return [
            'message' => 'Import has been queued successfully',
            'type' => 'csv'
        ];
    }
}
