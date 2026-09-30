<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Writing;

use Semilore\CsvImportPipeline\Domain\RowContext;

final class OutputWriter implements AcceptedRowWriter
{
    /** @var resource */
    private $handle;

    /** @param list<string> $fieldOrder */
    public function __construct(string $filePath, private readonly array $fieldOrder)
    {
        $handle = fopen($filePath, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open output CSV: {$filePath}");
        }

        $this->handle = $handle;
        fputcsv($this->handle, $fieldOrder, ',', '"', '\\');
    }

    public function write(RowContext $row): void
    {
        $values = [];

        foreach ($this->fieldOrder as $fieldName) {
            $values[] = $row->field($fieldName)->sanitized;
        }

        fputcsv($this->handle, $values, ',', '"', '\\');
    }

    public function close(): void
    {
        fclose($this->handle);
    }
}
