<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Reporting;

final class ImportReport
{
    private int $importedCount = 0;

    private array $rejections = [];

    public function recordImported(): void
    {
        $this->importedCount++;
    }

    public function recordRejected(int $rowNumber, array $reasons): void
    {
        $this->rejections[] = ['row' => $rowNumber, 'reasons' => $reasons];
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function rejectedCount(): int
    {
        return count($this->rejections);
    }

    /** @return list<array{row: int, reasons: list<string>}> */
    public function rejections(): array
    {
        return $this->rejections;
    }
}