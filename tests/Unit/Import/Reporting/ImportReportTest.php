<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Reporting\ImportReport;

it('tracks the count of imported rows', function () {
    $report = new ImportReport();

    $report->recordImported();
    $report->recordImported();

    expect($report->importedCount())->toBe(2);
});

it('tracks rejected rows with their row number and reasons', function () {
    $report = new ImportReport();

    $report->recordRejected(3, ['name: Name is required', 'balance: Balance must not be negative']);

    expect($report->rejectedCount())->toBe(1);
    expect($report->rejections())->toBe([
        ['row' => 3, 'reasons' => ['name: Name is required', 'balance: Balance must not be negative']],
    ]);
});

it('keeps imported and rejected counts independent of each other', function () {
    $report = new ImportReport();

    $report->recordImported();
    $report->recordRejected(2, ['email: Email is required']);
    $report->recordImported();

    expect($report->importedCount())->toBe(2);
    expect($report->rejectedCount())->toBe(1);
});