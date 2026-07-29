<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Sanitization;

interface SanitizesField
{
    public function sanitize(string $raw): string;
}
