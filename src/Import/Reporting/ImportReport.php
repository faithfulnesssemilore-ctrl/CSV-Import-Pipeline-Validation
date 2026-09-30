<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Reporting;

final class ImportReport
{
    private int $importedCount = 0;

    /** @var list<array{row: int, values: list<string>, reasons: list<string>}> */
    private array $rejections = [];

    public function recordImported(): void
    {
        $this->importedCount++;
    }

    /**
     * @param  list<string>  $reasons
     * @param  list<string>  $values
     */
    public function recordRejected(int $rowNumber, array $reasons, array $values = []): void
    {
        $this->rejections[] = [
            'row' => $rowNumber,
            'values' => $values,
            'reasons' => $reasons,
        ];
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function rejectedCount(): int
    {
        return count($this->rejections);
    }

    /** @return list<array{row: int, values: list<string>, reasons: list<string>}> */
    public function rejections(): array
    {
        return $this->rejections;
    }

    /**
     * @return array{imported: int, rejected: int, rejections: list<array{row: int, values: list<string>, reasons: list<string>}>}
     */
    public function toArray(): array
    {
        return [
            'imported' => $this->importedCount(),
            'rejected' => $this->rejectedCount(),
            'rejections' => $this->rejections(),
        ];
    }
}
