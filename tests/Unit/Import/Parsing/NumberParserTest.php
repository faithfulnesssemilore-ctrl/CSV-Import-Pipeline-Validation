<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Parsing\NumberParser;

it('successfully parses a valid decimal number', function () {
    $result = (new NumberParser())->parse('42.50');

    expect($result->success)->toBeTrue();
    expect($result->value)->toBe(42.50);
    expect($result->error)->toBeNull();
});

it('fails to parse a non-numeric string', function () {
    $result = (new NumberParser())->parse('not-a-number');

    expect($result->success)->toBeFalse();
    expect($result->value)->toBeNull();
    expect($result->error)->toContain('not-a-number');
});