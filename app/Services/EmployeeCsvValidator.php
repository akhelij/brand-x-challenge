<?php

namespace App\Services;

use DateTime;

class EmployeeCsvValidator
{
    //This might be better if we have a mapper for the headers, and we can use our own attributes, similar to employee table columns
    private array $requiredHeaders = [
        'Emp ID',
        'User Name',
        'Name Prefix',
        'First Name',
        'Middle Initial',
        'Last Name',
        'Gender',
        'E Mail',
        'Date of Birth',
        'Time of Birth',
        'Age in Yrs.',
        'Date of Joining',
        'Age in Company (Years)',
        'Phone No. ',
        'Place Name',
        'County',
        'City',
        'Zip',
        'Region'
    ];

    public function validate(array $headers, array $data = null): array
    {
        $errors = $this->validateHeaders($headers);

        if (!empty($data)) {
            $dataErrors = $this->validateData($data);
            if (!empty($dataErrors)) {
                $errors['data_validation'] = $dataErrors;
            }
        }

        return $errors;
    }

    private function validateHeaders(array $headers): array
    {
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        $extraHeaders = array_diff($headers, $this->requiredHeaders);

        $errors = [];

        if (!empty($missingHeaders)) {
            $errors['missing_headers'] = $missingHeaders;
        }

        if (!empty($extraHeaders)) {
            $errors['extra_headers'] = $extraHeaders;
        }

        return $errors;
    }

    //Of course, I used AI to make this code, I'm not a robot
    private function validateData(array $data): array
    {
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            $rowErrors = [];

            // Validate Import Reference (Emp ID)
            if (empty($row['Emp ID']) || !preg_match('/^\d+$/', $row['Emp ID'])) {
                $rowErrors['Emp ID'] = 'Invalid Employee ID format';
            }

            // Validate Email
            if (empty($row['E Mail']) || !filter_var($row['E Mail'], FILTER_VALIDATE_EMAIL)) {
                $rowErrors['E Mail'] = 'Invalid email format';
            }

            // Validate Required Names
            if (empty($row['First Name'])) {
                $rowErrors['First Name'] = 'First name is required';
            }
            if (empty($row['Last Name'])) {
                $rowErrors['Last Name'] = 'Last name is required';
            }

            // Validate Gender
            if (!empty($row['Gender']) && !in_array($row['Gender'], ['M', 'F'])) {
                $rowErrors['Gender'] = 'Gender must be M or F';
            }

            // Validate Dates
            if (!$this->isValidDate($row['Date of Birth'])) {
                $rowErrors['Date of Birth'] = 'Invalid date format. Expected format: M/D/YYYY';
            }
            if (!$this->isValidDate($row['Date of Joining'])) {
                $rowErrors['Date of Joining'] = 'Invalid date format. Expected format: M/D/YYYY';
            }

            // Validate Time
            if (!empty($row['Time of Birth']) && !$this->isValidTime($row['Time of Birth'])) {
                $rowErrors['Time of Birth'] = 'Invalid time format. Expected format: H:MM:SS AM/PM';
            }

            // Validate Numeric Values
            if (!empty($row['Age in Yrs.']) && !is_numeric($row['Age in Yrs.'])) {
                $rowErrors['Age in Yrs.'] = 'Age must be a number';
            }
            if (!empty($row['Age in Company (Years)']) && !is_numeric($row['Age in Company (Years)'])) {
                $rowErrors['Age in Company (Years)'] = 'Company years must be a number';
            }

            // Validate ZIP
            if (!empty($row['Zip']) && !preg_match('/^\d{5}$/', $row['Zip'])) {
                $rowErrors['Zip'] = 'Invalid ZIP code format';
            }

            if (!empty($rowErrors)) {
                $errors[$rowIndex + 1] = $rowErrors;  // +1 for human-readable row numbers
            }
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        try {
            return (bool) DateTime::createFromFormat('n/j/Y', $date);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isValidTime(string $time): bool
    {
        try {
            return (bool) DateTime::createFromFormat('g:i:s A', $time);
        } catch (\Exception $e) {
            return false;
        }
    }
}
