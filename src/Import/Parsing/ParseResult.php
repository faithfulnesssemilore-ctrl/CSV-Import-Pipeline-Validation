<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Parsing;

final class ParseResult
{
    private function __construct(
        public readonly bool $success,
        public readonly mixed $value,
        public readonly ?string $error,
    ) {
    }

    public static function success(mixed $value): self
    {
        return new self(success: true, value: $value, error: null);
    }

    public static function failure(string $error): self
    {
        return new self(success: false, value: null, error: $error);
    }
}
