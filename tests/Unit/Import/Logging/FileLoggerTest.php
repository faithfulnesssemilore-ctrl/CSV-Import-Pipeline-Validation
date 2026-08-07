<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Logging\FileLogger;

it('writes a timestamped log line to the file', function () {
    $path = sys_get_temp_dir() . '/logger_test_' . uniqid() . '.log';

    $logger = new FileLogger($path);
    $logger->log('Import started');
    $logger->close();

    $contents = file_get_contents($path);
    unlink($path);

    expect($contents)->toMatch('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] Import started\n$/');
});

it('appends new log lines without erasing previous ones across multiple loggers', function () {
    $path = sys_get_temp_dir() . '/logger_test_' . uniqid() . '.log';

    $first = new FileLogger($path);
    $first->log('First run finished');
    $first->close();

    $second = new FileLogger($path);
    $second->log('Second run finished');
    $second->close();

    $contents = file_get_contents($path);
    unlink($path);

    expect($contents)->toContain('First run finished');
    expect($contents)->toContain('Second run finished');
});