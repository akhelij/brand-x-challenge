<?php

namespace App\Http\Controllers\Api\V1\Imports;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeImportRequest;
use App\Factories\ImportStrategyFactory;
use App\Services\Import\ImportService;
use Illuminate\Support\Facades\Storage;

class EmployeeImportController extends Controller
{
    public function __construct(
        private ImportService $importService,
        private ImportStrategyFactory $strategyFactory
    ) {}

    public function __invoke(EmployeeImportRequest $request)
    {
        try {
            list($type, $source) = $this->getTypeAndSource($request);

            $strategy = $this->strategyFactory->create($type, $source);

            $this->importService->setStrategy($strategy);
            $result = $this->importService->execute();

            return response()->json($result, 202);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //This function is not perfect, It helps me demonstrate the strategy pattern I have used,
    // and ensure the `curl -X POST -H 'Content-Type: text/csv' --data-binary @import.csv http://{yourapp}/api/employee` to work
    private function getTypeAndSource($request) {
        if(! $request->has('type'))
        {
            throw(new \Exception('No type provided'));
        }

        $type = $request->type;

        if ($type === 'api') {
            return [$type, $request->get('source')];
        }

        if ($request->hasFile('file')) {
            return [$type, $request->file('file')->path()];
        }

        // In case its data binary like what you have asked
        $content = $request->getContent();
        if (empty($content)) {
            return response()->json(['error' => 'No content provided'], 400);
        }

        $source = Storage::disk('local')->path('temp-import.'.$request->type);
        file_put_contents($source, $content);

        return [$type, $source];
    }
}
