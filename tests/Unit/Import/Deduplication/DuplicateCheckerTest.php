<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Deduplication\DuplicateChecker;

it('returns false the first time a key is seen', function () {
    $checker = new DuplicateChecker(keyField: 'email');

    expect($checker->isDuplicate('ada@example.com'))->toBeFalse();
});

it('returns true when the same key is seen a second time', function () {
    $checker = new DuplicateChecker(keyField: 'email');

    $checker->isDuplicate('ada@example.com');

    expect($checker->isDuplicate('ada@example.com'))->toBeTrue();
});

it('treats different keys as independent, not duplicates of each other', function () {
    $checker = new DuplicateChecker(keyField: 'email');

    expect($checker->isDuplicate('ada@example.com'))->toBeFalse();
    expect($checker->isDuplicate('grace@example.com'))->toBeFalse();
});