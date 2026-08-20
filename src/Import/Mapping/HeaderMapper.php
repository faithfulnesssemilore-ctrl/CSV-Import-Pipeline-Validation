<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Mapping;

use RuntimeException;

final class HeaderMapper
{
   
    public function __construct(
        private readonly array $aliases,
    ) {
    }

   
    public function resolve(array $headerRow): array
    {
        $columnMap = [];

        foreach ($headerRow as $columnIndex => $rawLabel) {
            $canonicalName = $this->findCanonicalName(trim($rawLabel));

            if ($canonicalName !== null) {
                $columnMap[$columnIndex] = $canonicalName;
            }
        }

        $this->assertAllRequiredFieldsFound($columnMap);

        return $columnMap;
    }

    private function findCanonicalName(string $rawLabel): ?string
    {
        foreach ($this->aliases as $canonicalName => $acceptedLabels) {
            if (in_array($rawLabel, $acceptedLabels, strict: true)) {
                return $canonicalName;
            }
        }

        return null;
    }

    private function assertAllRequiredFieldsFound(array $columnMap): void
    {
        $foundFields = array_values($columnMap);
        $missingFields = array_diff(array_keys($this->aliases), $foundFields);

        if ($missingFields !== []) {
            $missingList = implode(', ', $missingFields);

            throw new RuntimeException("CSV header is missing required field(s): {$missingList}");
        }
    }
}