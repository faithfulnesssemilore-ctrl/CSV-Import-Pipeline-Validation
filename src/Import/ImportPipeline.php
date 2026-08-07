<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import;

use Semilore\CsvImportPipeline\Domain\FieldContext;
use Semilore\CsvImportPipeline\Domain\RowContext;
use Semilore\CsvImportPipeline\Import\Deduplication\DuplicateChecker;
use Semilore\CsvImportPipeline\Import\Logging\LogsImport;
use Semilore\CsvImportPipeline\Import\Mapping\HeaderMapper;
use Semilore\CsvImportPipeline\Import\Parsing\ParsesField;
use Semilore\CsvImportPipeline\Import\Reading\CsvReader;
use Semilore\CsvImportPipeline\Import\Reporting\ImportReport;
use Semilore\CsvImportPipeline\Import\Sanitization\SanitizesField;
use Semilore\CsvImportPipeline\Import\Validation\ValidationEngine;
use Semilore\CsvImportPipeline\Import\Writing\OutputWriter;
use Semilore\CsvImportPipeline\Import\Writing\RejectWriter;

final class ImportPipeline
{
    /**
     * @param array<string, SanitizesField> $sanitizers canonical field name => sanitizer
     * @param array<string, ParsesField> $parsers canonical field name => parser
     */
    public function __construct(
        private readonly CsvReader $reader,
        private readonly HeaderMapper $headerMapper,
        private readonly array $sanitizers,
        private readonly array $parsers,
        private readonly ValidationEngine $validationEngine,
        private readonly DuplicateChecker $duplicateChecker,
        private readonly string $duplicateCheckField,
        private readonly OutputWriter $outputWriter,
        private readonly RejectWriter $rejectWriter,
        private readonly LogsImport $logger,
        ) {
    }

    public function run(): ImportReport
    {
          $this->logger->log('Import started');
        $report = new ImportReport();
        $columnMap = null;
        $rowNumber = 0;

        foreach ($this->reader->rows() as $rawRow) {
            $rowNumber++;

            if ($columnMap === null) {
                $columnMap = $this->headerMapper->resolve($rawRow);
                continue;
            }

            $row = $this->buildRowContext($rowNumber, $rawRow, $columnMap);

            $this->validationEngine->validate($row);

            if (!$row->isValid()) {
                $report->recordRejected($rowNumber, $row->allErrors());
                $this->rejectWriter->write($rowNumber, $row->allErrors());
                continue;
            }

            $keyValue = $row->field($this->duplicateCheckField)->sanitized ?? '';

            if ($this->duplicateChecker->isDuplicate($keyValue)) {
                $report->recordRejected($rowNumber, ['Duplicate row']);
                $this->rejectWriter->write($rowNumber, ['Duplicate row']);
                continue;
            }

            $report->recordImported();
            $this->outputWriter->write($row);
        }

        $this->outputWriter->close();
        $this->rejectWriter->close();
        $this->logger->log("Import finished: {$report->importedCount()} imported, {$report->rejectedCount()} rejected");

        $totalRows = $report->importedCount() + $report->rejectedCount();

        if ($totalRows > 0 && ($report->rejectedCount() / $totalRows) > 0.5) {
            $this->logger->log("WARNING: high rejection rate — {$report->rejectedCount()} of {$totalRows} rows rejected. Possible configuration issue.");
        }

        return $report;
    }

    /**
     * @param list<string> $rawRow
     * @param array<int, string> $columnMap
     */
    private function buildRowContext(int $rowNumber, array $rawRow, array $columnMap): RowContext
    {
        $row = new RowContext(rowNumber: $rowNumber);

        foreach ($columnMap as $columnIndex => $fieldName) {
            $rawValue = $rawRow[$columnIndex] ?? '';

            $sanitized = $this->sanitizers[$fieldName]->sanitize($rawValue);
            $parseResult = $this->parsers[$fieldName]->parse($sanitized);

            $field = new FieldContext(
                raw: $rawValue,
                sanitized: $sanitized,
                typed: $parseResult->value,
                parseSuccess: $parseResult->success,
            );

            if (!$parseResult->success) {
    $field->addError("[parse] {$parseResult->error}");
}
            $row->setField($fieldName, $field);
        }

        return $row;
    }
}