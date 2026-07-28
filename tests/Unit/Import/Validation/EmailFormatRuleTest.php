<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Import\Validation\EmailFormatRule;

it('fails when the sanitized value is not shaped like an email', function () {
    $field = new FieldContext(raw: 'grace[at]example.com', sanitized: 'grace[at]example.com');
    $rule = new EmailFormatRule(fieldLabel: 'Email');

    expect($rule->validate($field))->toBe('Email is not a valid email address');
});

it('passes when the sanitized value is a properly shaped email', function () {
    $field = new FieldContext(raw: 'ada@example.com', sanitized: 'ada@example.com');
    $rule = new EmailFormatRule(fieldLabel: 'Email');

    expect($rule->validate($field))->toBeNull();
});

it('does not flag an empty value, leaving that to RequiredRule', function () {
    $field = new FieldContext(raw: '', sanitized: '');
    $rule = new EmailFormatRule(fieldLabel: 'Email');

    expect($rule->validate($field))->toBeNull();
});