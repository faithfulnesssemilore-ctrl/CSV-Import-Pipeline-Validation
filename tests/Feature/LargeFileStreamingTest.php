<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Reading\CsvReader;

it('streams a large file without loading it entirely into memory', function () {
    $path = sys_get_temp_dir() . '/large_csv_smoke_test_' . uniqid() . '.csv';
    $handle = fopen($path, 'w');

    fputcsv($handle, ['name', 'email'], ',', '"', '\\');

    $rowCount = 100_000;

    for ($i = 0; $i < $rowCount; $i++) {
        fputcsv($handle, ["Person {$i}", "person{$i}@example.com"], ',', '"', '\\');
    }

    fclose($handle);

    $memoryBefore = memory_get_usage();

    $reader = new CsvReader($path);
    $processedCount = 0;

    foreach ($reader->rows() as $row) {
        $processedCount++;
    }

    $memoryAfter = memory_get_usage();
    $memoryGrowth = $memoryAfter - $memoryBefore;

    unlink($path);

    expect($processedCount)->toBe($rowCount + 1); // +1 for the header row
    expect($memoryGrowth)->toBeLessThan(5 * 1024 * 1024); // under 5MB growth
});