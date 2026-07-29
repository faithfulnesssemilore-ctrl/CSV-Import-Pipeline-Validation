<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Parsing;

use DateTimeImmutable;

final class DateParser implements ParsesField
{
    public function __construct(
        private readonly string $expectedFormat = 'Y-m-d',
    ) {
    }

    public function parse(string $sanitized): ParseResult
    {
        $date = DateTimeImmutable::createFromFormat($this->expectedFormat, $sanitized);

        if ($date === false) {
            return ParseResult::failure("'{$sanitized}' does not match expected date format '{$this->expectedFormat}'");
        }

        return ParseResult::success($date);
    }
}