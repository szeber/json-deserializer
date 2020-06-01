<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use InvalidArgumentException;
use Yapep\JsonDeserializer\Exception\TypeViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

class ScalarRule extends RuleAbstract
{
    public const TYPE_INT    = 'int';
    public const TYPE_FLOAT  = 'float';
    public const TYPE_STRING = 'string';
    public const TYPE_BOOL   = 'bool';

    public const VALID_TYPES = [
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
        self::TYPE_BOOL,
    ];

    private string $fieldType;

    public function __construct(string $fieldName, bool $isRequired, bool $isNullable, string $fieldType)
    {
        parent::__construct($fieldName, $isRequired, $isNullable);

        $this->setFieldType($fieldType);
    }

    private function setFieldType(string $fieldType)
    {
        if (!in_array($fieldType, self::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid scalar field type: ' . $fieldType);
        }

        $this->fieldType = $fieldType;
    }

    protected function getDeserializedValue(
        JsonDeserializer $deserializer,
        $data,
        $value,
        string $deserializedClassName,
        string $prefixedFieldName
    ) {
        if (!$this->isValueValid($value)) {
            throw new TypeViolationException(
                $deserializedClassName,
                $prefixedFieldName,
                $this->fieldType,
                json_encode($value),
                $data
            );
        }

        return $this->getCastValue($value);
    }

    private function isValueValid($value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        switch ($this->fieldType) {
            case self::TYPE_BOOL:
                return is_bool($value);

            case self::TYPE_FLOAT:
            case self::TYPE_INT:
                return is_numeric($value);

            default:
                return true;
        }
    }

    private function getCastValue($value)
    {
        switch ($this->fieldType) {
            case self::TYPE_BOOL:
                return (bool)$value;

            case self::TYPE_INT:
                return (int)$value;

            case self::TYPE_FLOAT:
                return (float)$value;

            case self::TYPE_STRING:
                return (string)$value;

            default:
                throw new InvalidArgumentException('Unknown scalar field type: ' . $this->fieldType);
        }
    }
}
