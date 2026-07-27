<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Parsing\DateParser;

it('successfully parses a valid ISO date', function () {
    $result = (new DateParser())->parse('2021-03-01');

    expect($result->success)->toBeTrue();
    expect($result->value->format('Y-m-d'))->toBe('2021-03-01');
});

it('fails to parse a date in the wrong format', function () {
    $result = (new DateParser())->parse('03/14/1906');

    expect($result->success)->toBeFalse();
    expect($result->value)->toBeNull();
    expect($result->error)->toContain('03/14/1906');
});
