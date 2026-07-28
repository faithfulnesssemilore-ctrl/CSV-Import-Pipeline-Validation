<?php

declare(strict_types=1);

use Semilore\CsvImportPipeline\Import\Deduplication\DuplicateChecker;
use Semilore\CsvImportPipeline\Import\ImportPipeline;
use Semilore\CsvImportPipeline\Import\Mapping\HeaderMapper;
use Semilore\CsvImportPipeline\Import\Parsing\DateParser;
use Semilore\CsvImportPipeline\Import\Parsing\NumberParser;
use Semilore\CsvImportPipeline\Import\Sanitization\EmailSanitizer;
use Semilore\CsvImportPipeline\Import\Sanitization\TrimSanitizer;
use Semilore\CsvImportPipeline\Import\Validation\NonNegativeRule;
use Semilore\CsvImportPipeline\Import\Validation\RequiredRule;
use Semilore\CsvImportPipeline\Import\Validation\ValidationEngine;
use Semilore\CsvImportPipeline\Import\Reading\CsvReader;
use Semilore\CsvImportPipeline\Import\Writing\OutputWriter;
use Semilore\CsvImportPipeline\Import\Writing\RejectWriter;

it('imports valid rows and rejects invalid ones from the assignment sample CSV', function () {
    $inputPath = sys_get_temp_dir() . '/pipeline_input_' . uniqid() . '.csv';
    $outputPath = sys_get_temp_dir() . '/pipeline_output_' . uniqid() . '.csv';
    $rejectPath = sys_get_temp_dir() . '/pipeline_reject_' . uniqid() . '.csv';

    file_put_contents($inputPath, <<<CSV
    name,email,signup_date,balance
    Ada Lovelace,ada@example.com,2021-03-01,42.50
    Grace Hopper,grace[at]example.com,03/14/1906,not-a-number
    ,bob@example.com,2022-13-40,10.00
    CSV);

    $pipeline = new ImportPipeline(
        reader: new CsvReader($inputPath),
        headerMapper: new HeaderMapper([
            'name' => ['name'],
            'email' => ['email'],
            'signup_date' => ['signup_date'],
            'balance' => ['balance'],
        ]),
        sanitizers: [
            'name' => new TrimSanitizer(),
            'email' => new EmailSanitizer(),
            'signup_date' => new TrimSanitizer(),
            'balance' => new TrimSanitizer(),
        ],
        parsers: [
            'name' => new class implements \Semilore\CsvImportPipeline\Import\Parsing\ParsesField {
                public function parse(string $sanitized): \Semilore\CsvImportPipeline\Import\Parsing\ParseResult {
                    return \Semilore\CsvImportPipeline\Import\Parsing\ParseResult::success($sanitized);
                }
            },
            'email' => new class implements \Semilore\CsvImportPipeline\Import\Parsing\ParsesField {
                public function parse(string $sanitized): \Semilore\CsvImportPipeline\Import\Parsing\ParseResult {
                    return \Semilore\CsvImportPipeline\Import\Parsing\ParseResult::success($sanitized);
                }
            },
            'signup_date' => new DateParser(expectedFormat: 'Y-m-d'),
            'balance' => new NumberParser(),
        ],
        validationEngine: new ValidationEngine([
            'name' => [new RequiredRule(fieldLabel: 'Name')],
            'email' => [new RequiredRule(fieldLabel: 'Email')],
            'balance' => [new NonNegativeRule(fieldLabel: 'Balance')],
        ]),
        duplicateChecker: new DuplicateChecker(keyField: 'email'),
        duplicateCheckField: 'email',
        outputWriter: new OutputWriter($outputPath, ['name', 'email', 'signup_date', 'balance']),
        rejectWriter: new RejectWriter($rejectPath),
    );

    $report = $pipeline->run();

    unlink($inputPath);
    unlink($outputPath);
    unlink($rejectPath);

    expect($report->importedCount())->toBe(1);
    expect($report->rejectedCount())->toBe(2);
});