<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\FieldContext;

final class EmailFormatRule implements ValidatesField
{
    public function __construct(
        private readonly string $fieldLabel,
    ) {
    }

    public function validate(FieldContext $field): ?string
    {
        if ($field->sanitized === null || $field->sanitized === '') {
            return null;
        }

        if (filter_var($field->sanitized, FILTER_VALIDATE_EMAIL) === false) {
            return "{$this->fieldLabel} is not a valid email address";
        }

        return null;
    }
}