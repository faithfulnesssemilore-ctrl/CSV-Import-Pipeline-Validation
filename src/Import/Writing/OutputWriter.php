<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Writing;

use Semilore\CsvImportPipeline\Domain\RowContext;

final class OutputWriter
{
    /** @var resource */
    private $handle;

    public function __construct(string $filePath, private readonly array $fieldOrder)
    {
        $this->handle = fopen($filePath, 'w');
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