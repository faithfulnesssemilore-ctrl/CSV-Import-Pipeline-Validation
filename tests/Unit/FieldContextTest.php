<?php

use Semilore\CsvImportPipeline\Domain\FieldContext;



it('stores the raw value and starts with no errors', function () {
    $field = new FieldContext(raw: 'not-a-number');

    expect($field->raw)->toBe('not-a-number');
    expect($field->hasErrors())->toBeFalse();
    expect($field->errors())->toBe([]);
});

it('accumulates multiple errors added over time', function () {
    $field = new FieldContext(raw: '-5.00');

    $field->addError('Balance is not a valid number');
    $field->addError('Balance must be non-negative');

    expect($field->errors())->toHaveCount(2);
    expect($field->hasErrors())->toBeTrue();
});

it('does not allow raw to be changed after construction', function () {
    $field = new FieldContext(raw: '42.50');

    expect(fn () => $field->raw = 'tampered')
        ->toThrow(Error::class);
});