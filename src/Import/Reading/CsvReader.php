<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Reading;

use RuntimeException;

final class CsvReader
{
    public function __construct(
        private readonly string $filePath,
    ) {
    }

    
    public function rows(): \Generator
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("File does not exist: {$this->filePath}");
        }

        $handle = fopen($this->filePath, 'r');

        if ($handle === false) {
            throw new RuntimeException("Unable to open file: {$this->filePath}");
        }

        try {
            while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                yield $row;
            }
        } finally {
            fclose($handle);
        }
    }
}