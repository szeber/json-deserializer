<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Yapep\JsonDeserializer\Exception\EmptinessViolationException;
use Yapep\JsonDeserializer\Exception\TypeViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

class ArrayRule extends RuleAbstract implements IUnpackable
{
    private bool $isEmptyAllowed;

    private bool $isUnpacked;

    private IRule $valueRule;

    public function __construct(
        string $fieldName,
        bool $isRequired,
        bool $isNullable,
        bool $isEmptyAllowed,
        IRule $valueRule,
        bool $isUnpacked = false
    ) {
        parent::__construct($fieldName, $isRequired, $isNullable);

        $this->isEmptyAllowed = $isEmptyAllowed;
        $this->valueRule      = $valueRule;
        $this->isUnpacked     = $isUnpacked;
    }

    public function isUnpacked(): bool
    {
        return $this->isUnpacked;
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
                'array',
                json_encode($value),
                $data
            );
        }

        if (!$this->isEmptyAllowed && empty($value)) {
            throw new EmptinessViolationException(
                $deserializedClassName,
                $prefixedFieldName,
                'array',
                json_encode($value),
                $data
            );
        }

        $result = [];

        foreach ($value as $key => $valueRow) {
            $result[$key] = $this->valueRule->deserializeValue(
                $deserializer,
                $valueRow,
                $deserializedClassName,
                $this->getPrefixedFieldName($prefixedFieldName, (string)$key)
            );
        }

        return $result;
    }
}
