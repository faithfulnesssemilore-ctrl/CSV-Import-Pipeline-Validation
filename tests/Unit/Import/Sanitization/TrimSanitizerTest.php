<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Sanitization\TrimSanitizer;

it('trims leading and trailing whitespace', function () {
    $sanitizer = new TrimSanitizer();

    expect($sanitizer->sanitize('  Ada Lovelace  '))->toBe('Ada Lovelace');
});

it('leaves already-clean input unchanged', function () {
    $sanitizer = new TrimSanitizer();

    expect($sanitizer->sanitize('Ada Lovelace'))->toBe('Ada Lovelace');
});