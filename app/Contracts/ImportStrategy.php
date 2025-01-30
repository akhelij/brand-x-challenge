<?php

namespace App\Contracts;

interface ImportStrategy
{
    public function import(): array;
}
