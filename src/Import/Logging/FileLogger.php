<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Logging;

final class FileLogger implements LogsImport
{
    /** @var resource */
    private $handle;

    public function __construct(string $filePath)
    {
        $this->handle = fopen($filePath, 'a');
    }

    public function log(string $message): void
    {
        $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        fwrite($this->handle, "[{$timestamp}] {$message}\n");
    }

    public function close(): void
    {
        fclose($this->handle);
    }
}