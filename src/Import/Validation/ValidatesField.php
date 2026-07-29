<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\FieldContext;

interface ValidatesField
{
    public function validate(FieldContext $field): ?string;
}

