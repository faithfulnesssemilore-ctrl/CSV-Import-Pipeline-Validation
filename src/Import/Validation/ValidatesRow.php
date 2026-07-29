<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\RowContext;

interface ValidatesRow
{
    public function validate(RowContext $row): ?string;
}