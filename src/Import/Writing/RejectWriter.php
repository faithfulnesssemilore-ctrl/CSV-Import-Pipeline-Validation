<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Writing;

final class RejectWriter
{
    /** @var resource */
    private $handle;

    public function __construct(string $filePath)
    {
        $handle = fopen($filePath, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open reject CSV: {$filePath}");
        }

        $this->handle = $handle;
        fputcsv($this->handle, ['row_number', 'reasons'], ',', '"', '\\');
    }

    /** @param list<string> $reasons */
    public function write(int $rowNumber, array $reasons): void
    {
        fputcsv($this->handle, [$rowNumber, implode('; ', $reasons)], ',', '"', '\\');
    }

    public function close(): void
    {
        fclose($this->handle);
    }
}
