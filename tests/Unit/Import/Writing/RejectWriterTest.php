<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Writing\RejectWriter;

it('writes rejected rows with multiple reasons joined readably', function () {
    $path = sys_get_temp_dir() . '/reject_writer_test_' . uniqid() . '.csv';

    $writer = new RejectWriter($path);
    $writer->write(3, ['name: Name is required', 'balance: Balance must not be negative']);
    $writer->close();

    $contents = file_get_contents($path);
    unlink($path);

    expect($contents)->toBe(
        "row_number,reasons\n3,\"name: Name is required; balance: Balance must not be negative\"\n"
    );
});