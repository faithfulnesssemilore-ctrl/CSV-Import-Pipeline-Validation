<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Sanitization;

final class TrimSanitizer implements SanitizesField
{
    public function sanitize(string $raw): string
    {
        return trim($raw);
    }
}
