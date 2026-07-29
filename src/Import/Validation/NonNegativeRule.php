<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\FieldContext;

final class NonNegativeRule implements ValidatesField
{
    public function __construct(
        private readonly string $fieldLabel,
    ) {
    }

    public function validate(FieldContext $field): ?string
    {
        if (!$field->parseSuccess) {
            return null;
        }

        if ($field->typed < 0) {
            return "{$this->fieldLabel} must not be negative";
        }

        return null;
    }
}