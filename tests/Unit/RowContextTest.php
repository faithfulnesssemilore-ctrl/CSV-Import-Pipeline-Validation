<?php

use Semilore\CsvImportPipeline\Domain\RowContext;
use Semilore\CsvImportPipeline\Domain\FieldContext;

it('is valid when it has no fields with errors', function () {
    $row = new RowContext(rowNumber: 1);
    $row->setField('name', new FieldContext(raw: 'Ada Lovelace'));
    expect($row->isValid())->toBeTrue();
});

it('is invalid when at least one field has an error', function () {
    $row = new RowContext(rowNumber: 2);
    $field = new FieldContext(raw: 'bad-data');
    $field->addError('This field is bad');
    $row->setField('email', $field);
    
    expect($row->isValid())->toBeFalse();
});

it('prefixes each error message with its field name', function () {
    $row = new RowContext(rowNumber: 3);
    
    $field1 = new FieldContext(raw: 'wrong-age');
    $field1->addError('must be a number');
    $row->setField('age', $field1);
    
    $field2 = new FieldContext(raw: 'bad-email');
    $field2->addError('invalid format');
    $row->setField('email', $field2);
    expect($row->allErrors())->toBe([
    'age: must be a number',
    'email: invalid format',
]);
});
