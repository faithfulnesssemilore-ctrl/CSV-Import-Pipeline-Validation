<?php
declare(strict_types=1);
namespace Semilore\CsvImportPipeline\Domain;

final class FieldContext
{
    private array $errors = [];

    public function __construct(
        public readonly string $raw,
        public readonly ?string $sanitized = null,
        public readonly mixed $typed = null,
        public readonly bool $parseSuccess = false,
    ) {
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}
