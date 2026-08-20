<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Configuration;

use RuntimeException;

final class ConfigurationValidator
{
  
    public function validate(array $aliases, array $sanitizers, array $parsers, string $duplicateCheckField): void
    {
        $canonicalFields = array_keys($aliases);

        foreach ($canonicalFields as $field) {
            if (!isset($sanitizers[$field])) {
                throw new RuntimeException("Invalid configuration: no sanitizer configured for field '{$field}'");
            }

            if (!isset($parsers[$field])) {
                throw new RuntimeException("Invalid configuration: no parser configured for field '{$field}'");
            }
        }

        foreach ($aliases as $field => $acceptedLabels) {
            if ($acceptedLabels === []) {
                throw new RuntimeException("Invalid configuration: field '{$field}' has no accepted header labels");
            }
        }

        if (!in_array($duplicateCheckField, $canonicalFields, strict: true)) {
            throw new RuntimeException("Invalid configuration: duplicate check field '{$duplicateCheckField}' is not a configured field");
        }
    }
}