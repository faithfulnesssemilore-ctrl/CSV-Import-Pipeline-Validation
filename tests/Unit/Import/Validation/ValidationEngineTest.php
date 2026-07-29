<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Domain\RowContext;
use Semilore\CsvImportPipeline\Import\Validation\NonNegativeRule;
use Semilore\CsvImportPipeline\Import\Validation\RequiredRule;
use Semilore\CsvImportPipeline\Import\Validation\ValidationEngine;

it('collects all errors across multiple fields in one pass', function () {
    $row = new RowContext(rowNumber: 3);
    $row->setField('name', new FieldContext(raw: '', sanitized: ''));
    $row->setField('balance', new FieldContext(raw: '-5.00', sanitized: '-5.00', typed: -5.00, parseSuccess: true));

    $engine = new ValidationEngine([
        'name' => [new RequiredRule(fieldLabel: 'Name')],
        'balance' => [new NonNegativeRule(fieldLabel: 'Balance')],
    ]);

    $engine->validate($row);

    expect($row->isValid())->toBeFalse();
    expect($row->allErrors())->toBe([
        'name: Name is required',
        'balance: Balance must not be negative',
    ]);
});

it('leaves a fully valid row with no errors', function () {
    $row = new RowContext(rowNumber: 1);
    $row->setField('name', new FieldContext(raw: 'Ada', sanitized: 'Ada'));
    $row->setField('balance', new FieldContext(raw: '42.50', sanitized: '42.50', typed: 42.50, parseSuccess: true));

    $engine = new ValidationEngine([
        'name' => [new RequiredRule(fieldLabel: 'Name')],
        'balance' => [new NonNegativeRule(fieldLabel: 'Balance')],
    ]);

    $engine->validate($row);

    expect($row->isValid())->toBeTrue();
    expect($row->allErrors())->toBe([]);
});
