<?php

declare(strict_types=1);

namespace Semilore\CsvImportPipeline\Import\Validation;

use Semilore\CsvImportPipeline\Domain\RowContext;

final class ValidationEngine
{
    /** @var array<string, list<ValidatesField>> */
    private array $rulesByField;

    /**
     * @param array<string, list<ValidatesField>> $rulesByField
     */
    public function __construct(array $rulesByField)
    {
        $this->rulesByField = $rulesByField;
    }

    public function validate(RowContext $row): void
    {
        foreach ($row->fields() as $fieldName => $field) {
            $rules = $this->rulesByField[$fieldName] ?? [];

            foreach ($rules as $rule) {
                $error = $rule->validate($field);

                if ($error !== null) {
                    $field->addError($error);
                }
            }
        }
    }
}
