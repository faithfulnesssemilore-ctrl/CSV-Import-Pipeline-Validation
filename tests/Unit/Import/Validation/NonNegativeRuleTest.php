<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Import\Validation\NonNegativeRule;

it('fails when the typed value is negative', function () {
    $field = new FieldContext(raw: '-5.00', sanitized: '-5.00', typed: -5.00, parseSuccess: true);
    $rule = new NonNegativeRule(fieldLabel: 'Balance');

    expect($rule->validate($field))->toBe('Balance must not be negative');
});

it('passes when the typed value is zero or positive', function () {
    $field = new FieldContext(raw: '42.50', sanitized: '42.50', typed: 42.50, parseSuccess: true);
    $rule = new NonNegativeRule(fieldLabel: 'Balance');

    expect($rule->validate($field))->toBeNull();
});

it('does not add a second error when parsing already failed', function () {
    $field = new FieldContext(raw: 'not-a-number', sanitized: 'not-a-number', typed: null, parseSuccess: false);
    $rule = new NonNegativeRule(fieldLabel: 'Balance');

    expect($rule->validate($field))->toBeNull();
});