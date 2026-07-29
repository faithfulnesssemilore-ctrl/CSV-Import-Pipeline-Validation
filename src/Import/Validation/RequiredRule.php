<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\FieldContext;

final class RequiredRule implements ValidatesField
{
    public function __construct(
        private readonly string $fieldLabel,
    ) {
    }

    public function validate(FieldContext $field): ?string
    {
        if ($field->sanitized === null || $field->sanitized === '') {
            return "{$this->fieldLabel} is required";
        }

        return null;
    }
}