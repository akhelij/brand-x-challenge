<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportEmployeesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($this->records as $data) {
                Employee::create([
                    'import_reference' => $data['Emp ID'],
                    'username' => $data['User Name'],
                    'name_prefix' => $data['Name Prefix'],
                    'first_name' => $data['First Name'],
                    'middle_initial' => $data['Middle Initial'],
                    'last_name' => $data['Last Name'],
                    'gender' => $data['Gender'],
                    'email' => $data['E Mail'],
                    'date_of_birth' => $data['Date of Birth'],
                    'time_of_birth' => $data['Time of Birth'],
                    'age_in_years' => $data['Age in Yrs.'],
                    'date_of_joining' => $data['Date of Joining'],
                    'age_in_company_years' => $data['Age in Company (Years)'],
                    'phone_number' => $data['Phone No. '],
                    'place_name' => $data['Place Name'],
                    'county' => $data['County'],
                    'city' => $data['City'],
                    'zip' => $data['Zip'],
                    'region' => $data['Region'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
