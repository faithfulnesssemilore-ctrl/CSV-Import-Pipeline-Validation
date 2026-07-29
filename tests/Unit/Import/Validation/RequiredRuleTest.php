<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Import\Validation\RequiredRule;

it('fails when the sanitized value is an empty string', function () {
    $field = new FieldContext(raw: '', sanitized: '');
    $rule = new RequiredRule(fieldLabel: 'Name');

    expect($rule->validate($field))->toBe('Name is required');
});

it('passes when the sanitized value is present', function () {
    $field = new FieldContext(raw: 'Ada', sanitized: 'Ada');
    $rule = new RequiredRule(fieldLabel: 'Name');

    expect($rule->validate($field))->toBeNull();
});
