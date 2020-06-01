<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Yapep\JsonDeserializer\Exception\TypeViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

class ClassRule extends RuleAbstract
{
    private string $className;

    public function __construct(string $fieldName, bool $isRequired, bool $isNullable, string $className)
    {
        parent::__construct($fieldName, $isRequired, $isNullable);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class does not exist: ' . $className);
        }

        $this->className = $className;
    }

    protected function getDeserializedValue(
        JsonDeserializer $deserializer,
        $data,
        $value,
        string $deserializedClassName,
        string $prefixedFieldName
    ) {
        if (!is_array($value)) {
            throw new TypeViolationException(
                $deserializedClassName,
                $prefixedFieldName,
                $this->className,
                json_encode($value),
                $data
            );
        }

        return $deserializer->deserializeToClassFromArray($this->className, $value, $prefixedFieldName);
    }
}
