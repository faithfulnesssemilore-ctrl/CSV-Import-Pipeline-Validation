<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Writing;

use Semilore\CsvImportPipeline\Domain\RowContext;

interface AcceptedRowWriter
{
    public function write(RowContext $row): void;

    public function close(): void;
}
