<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Domain\RowContext;
use Semilore\CsvImportPipeline\Import\Writing\OutputWriter;

it('writes a header row and correctly quotes values containing commas', function () {
    $path = sys_get_temp_dir() . '/output_writer_test_' . uniqid() . '.csv';

    $writer = new OutputWriter($path, ['name', 'email']);

    $row = new RowContext(rowNumber: 1);
    $row->setField('name', new FieldContext(raw: 'Smith, John', sanitized: 'Smith, John'));
    $row->setField('email', new FieldContext(raw: 'john@example.com', sanitized: 'john@example.com'));

    $writer->write($row);
    $writer->close();

    $contents = file_get_contents($path);
    unlink($path);

    expect($contents)->toBe("name,email\n\"Smith, John\",john@example.com\n");
});