<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Sanitization\EmailSanitizer;

it('lowercases and trims a messy email', function () {
    $sanitizer = new EmailSanitizer();

    expect($sanitizer->sanitize('  Ada@Example.COM  '))->toBe('ada@example.com');
});

it('leaves an already-clean email unchanged', function () {
    $sanitizer = new EmailSanitizer();

    expect($sanitizer->sanitize('ada@example.com'))->toBe('ada@example.com');
});