<?php 
declare(strict_types=1);
namespace Semilore\CsvImportPipeline\Domain;


final class RowContext
{
    private array $fields = [];

    public function __construct(
        public readonly int $rowNumber,
    ) {
    }

    public function setField(string $name, FieldContext $field): void
    {
        $this->fields[$name] = $field;
    }

    public function field(string $name): FieldContext
    {
        return $this->fields[$name];
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function isValid(): bool
    {
        foreach ($this->fields as $field) {
            if ($field->hasErrors()) {
                return false;
            }
        }

        return true;
    }

    public function allErrors(): array
    {
        $errors = [];

        foreach ($this->fields as $name => $field) {
            foreach ($field->errors() as $message) {
                $errors[] = "{$name}: {$message}";
            }
        }

        return $errors;
    }
}
