<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Domain\RowContext;
use Semilore\CsvImportPipeline\Import\Validation\SignupDateNotInFutureRule;

it('fails when signup_date is in the future', function () {
    $row = new RowContext(rowNumber: 1);
    $futureDate = new DateTimeImmutable('+1 year');
    $row->setField('signup_date', new FieldContext(
        raw: $futureDate->format('Y-m-d'),
        sanitized: $futureDate->format('Y-m-d'),
        typed: $futureDate,
        parseSuccess: true,
    ));

    $rule = new SignupDateNotInFutureRule();

    expect($rule->validate($row))->toBe('signup_date: Signup date cannot be in the future');
});

it('passes when signup_date is in the past', function () {
    $row = new RowContext(rowNumber: 2);
    $pastDate = new DateTimeImmutable('2021-03-01');
    $row->setField('signup_date', new FieldContext(
        raw: '2021-03-01',
        sanitized: '2021-03-01',
        typed: $pastDate,
        parseSuccess: true,
    ));

    $rule = new SignupDateNotInFutureRule();

    expect($rule->validate($row))->toBeNull();
});

it('does not add a second error when signup_date already failed to parse', function () {
    $row = new RowContext(rowNumber: 3);
    $row->setField('signup_date', new FieldContext(
        raw: '03/14/1906',
        sanitized: '03/14/1906',
        typed: null,
        parseSuccess: false,
    ));

    $rule = new SignupDateNotInFutureRule();

    expect($rule->validate($row))->toBeNull();
});