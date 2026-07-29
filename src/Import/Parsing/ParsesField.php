<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Parsing;

interface ParsesField
{
    public function parse(string $sanitized): ParseResult;
}