<?php

namespace App\Services\Import;

use App\Contracts\ImportStrategy;

class ImportService
{
    private ImportStrategy $strategy;

    public function setStrategy(\App\Contracts\ImportStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function execute(): array
    {
        return $this->strategy->import();
    }
}
