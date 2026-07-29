<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Deduplication;

final class DuplicateChecker
{
    /** @var array<string, true> */
    private array $seenKeys = [];

    public function __construct(
        private readonly string $keyField,
    ) {
    }

    public function isDuplicate(string $keyValue): bool
    {
        if (isset($this->seenKeys[$keyValue])) {
            return true;
        }

        $this->seenKeys[$keyValue] = true;

        return false;
    }
}