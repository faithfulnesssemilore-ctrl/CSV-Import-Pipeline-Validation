<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Configuration\ConfigurationValidator;

it('passes when every field has a sanitizer, parser, and at least one alias', function () {
    $validator = new ConfigurationValidator();

    $validator->validate(
        aliases: ['email' => ['email']],
        sanitizers: ['email' => new stdClass()],
        parsers: ['email' => new stdClass()],
        duplicateCheckField: 'email',
    );

    expect(true)->toBeTrue(); // no exception thrown = pass
});

it('throws when a field has no sanitizer configured', function () {
    $validator = new ConfigurationValidator();

    expect(fn () => $validator->validate(
        aliases: ['balance' => ['balance']],
        sanitizers: [],
        parsers: ['balance' => new stdClass()],
        duplicateCheckField: 'balance',
    ))->toThrow(RuntimeException::class, "no sanitizer configured for field 'balance'");
});

it('throws when the duplicate check field is not a configured field', function () {
    $validator = new ConfigurationValidator();

    expect(fn () => $validator->validate(
        aliases: ['email' => ['email']],
        sanitizers: ['email' => new stdClass()],
        parsers: ['email' => new stdClass()],
        duplicateCheckField: 'phone',
    ))->toThrow(RuntimeException::class, "duplicate check field 'phone' is not a configured field");
});