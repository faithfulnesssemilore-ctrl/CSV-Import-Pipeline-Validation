<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use DateTimeImmutable;
use Semilore\CsvImportPipeline\Domain\RowContext;

final class SignupDateNotInFutureRule implements ValidatesRow
{
    public function validate(RowContext $row): ?string
    {
        $signupDateField = $row->field('signup_date');

        if (!$signupDateField->parseSuccess) {
            return null;
        }

        if ($signupDateField->typed > new DateTimeImmutable('now')) {
            return 'signup_date: Signup date cannot be in the future';
        }

        return null;
    }
}