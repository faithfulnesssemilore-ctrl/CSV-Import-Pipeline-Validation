<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Logging;

interface LogsImport
{
    public function log(string $message): void;
}