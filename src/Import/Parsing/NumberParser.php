<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Parsing;

final class NumberParser implements ParsesField
{
    public function parse(string $sanitized): ParseResult
    {
        if (!is_numeric($sanitized)) {
            return ParseResult::failure("'{$sanitized}' is not a valid number");
        }

        return ParseResult::success((float) $sanitized);
    }
}