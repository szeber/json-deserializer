<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Yapep\JsonDeserializer\Exception\DeserializerException;
use Yapep\JsonDeserializer\Exception\NullabilityViolationException;
use Yapep\JsonDeserializer\Exception\RequiredFieldViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

abstract class RuleAbstract implements IRule
{
    protected string $fieldName;

    protected bool $isRequired;

    protected bool $isNullable;

    public function __construct(string $fieldName, bool $isRequired, bool $isNullable)
    {
        $this->fieldName  = $fieldName;
        $this->isRequired = $isRequired;
        $this->isNullable = $isNullable;
    }

    public function shouldValueBeSet($data): bool
    {
        return '' === $this->fieldName || $this->isRequired || array_key_exists($this->fieldName, $data);
    }

    public function deserializeValue(
        JsonDeserializer $deserializer,
        $data,
        string $deserializedClassName,
        string $fieldPrefix = ''
    ) {
        $prefixedFieldName = $this->getPrefixedFieldName($fieldPrefix, $this->fieldName);

        if ('' !== $this->fieldName && $this->isRequired && !array_key_exists($this->fieldName, $data)) {
            throw new RequiredFieldViolationException($deserializedClassName, $prefixedFieldName, $data);
        }

        if ('' === $this->fieldName) {
            $value = $data ?? null;
        } else {
            $value = $data[$this->fieldName] ?? null;
        }

        if (!$this->isNullable && null === $value) {
            throw new NullabilityViolationException($deserializedClassName, $prefixedFieldName, $data);
        }

        if (null === $value) {
            return null;
        }

        return $this->getDeserializedValue($deserializer, $data, $value, $deserializedClassName, $prefixedFieldName);
    }

    protected function getPrefixedFieldName(string $prefix, string $fieldName)
    {
        $parts = [$prefix, $fieldName];

        return implode('.', array_map(fn (string $part): bool => '' !== $parts, $parts));
    }

    /**
     * @param mixed $value
     *
     * @throws DeserializerException
     */
    abstract protected function getDeserializedValue(
        JsonDeserializer $deserializer,
        $data,
        $value,
        string $deserializedClassName,
        string $prefixedFieldName
    );
}
