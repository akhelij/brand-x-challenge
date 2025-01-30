<?php

namespace App\Factories;

use App\Contracts\ImportStrategy;
use App\Strategies\ApiImportStrategy;
use App\Strategies\CsvImportStrategy;
use Illuminate\Http\UploadedFile;

class ImportStrategyFactory
{
    public function create(string $type, mixed $source): ImportStrategy
    {
        return match ($type) {
            'csv' => new CsvImportStrategy($source instanceof UploadedFile ? $source->getRealPath() : $source),
//            'xml' => new XmlImportStrategy($source),
//            'xlsx' => new XlsxImportStrategy($source),
            'api' => new ApiImportStrategy($source),
            default => throw new \InvalidArgumentException("Unsupported type: $type"),
        };
    }
}
