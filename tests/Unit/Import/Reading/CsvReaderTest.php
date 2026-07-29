<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Reading\CsvReader;

it('yields each row of the CSV file one at a time', function () {
    $path = sys_get_temp_dir() . '/csv_reader_test_' . uniqid() . '.csv';

    file_put_contents($path, "name,email\nAda Lovelace,ada@example.com\nGrace Hopper,grace@example.com\n");

    $reader = new CsvReader($path);
    $rows = iterator_to_array($reader->rows());

    unlink($path);

    expect($rows)->toHaveCount(3);
    expect($rows[0])->toBe(['name', 'email']);
    expect($rows[1])->toBe(['Ada Lovelace', 'ada@example.com']);
    expect($rows[2])->toBe(['Grace Hopper', 'grace@example.com']);
});

it('throws a RuntimeException when the file does not exist', function () {
    $reader = new CsvReader('/nonexistent/path/does_not_exist.csv');

    expect(fn () => iterator_to_array($reader->rows()))
        ->toThrow(RuntimeException::class);
});