<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Mapping\HeaderMapper;

it('maps canonical field names to their column position', function () {
    $mapper = new HeaderMapper([
        'name' => ['name', 'Full Name'],
        'email' => ['email', 'Email Address'],
    ]);

    $columnMap = $mapper->resolve(['name', 'email']);

    expect($columnMap)->toBe([
        0 => 'name',
        1 => 'email',
    ]);
});

it('resolves vendor-specific aliases to the correct canonical name', function () {
    $mapper = new HeaderMapper([
        'name' => ['name', 'Full Name'],
        'email' => ['email', 'Email Address'],
    ]);

    $columnMap = $mapper->resolve(['Full Name', 'Email Address']);

    expect($columnMap)->toBe([
        0 => 'name',
        1 => 'email',
    ]);
});

it('correctly maps columns regardless of their order in the file', function () {
    $mapper = new HeaderMapper([
        'name' => ['name'],
        'email' => ['email'],
    ]);

    $columnMap = $mapper->resolve(['email', 'name']);

    expect($columnMap)->toBe([
        0 => 'email',
        1 => 'name',
    ]);
});

it('ignores columns that have no matching alias', function () {
    $mapper = new HeaderMapper([
        'name' => ['name'],
    ]);

    $columnMap = $mapper->resolve(['name', 'some_vendor_internal_id']);

    expect($columnMap)->toBe([
        0 => 'name',
    ]);
});

it('throws when a required canonical field is missing from the header entirely', function () {
    $mapper = new HeaderMapper([
        'name' => ['name'],
        'email' => ['email'],
    ]);

    expect(fn () => $mapper->resolve(['name']))
        ->toThrow(RuntimeException::class, 'CSV header is missing required field(s): email');
});