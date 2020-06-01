<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yapep\JsonDeserializer\Exception\TypeViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

class DateTimeRule extends RuleAbstract
{
    private bool $isImmutable;

    private string $formatString;

    private ?DateTimeZone $timeZone;

    public function __construct(
        string $fieldName,
        bool $isRequired,
        bool $isNullable,
        bool $isImmutable = false,
        string $formatString = DateTime::ATOM,
        ?DateTimeZone $timeZone = null
    ) {
        parent::__construct($fieldName, $isRequired, $isNullable);

        $this->isImmutable  = $isImmutable;
        $this->formatString = $formatString;
        $this->timeZone     = $timeZone;
    }

    protected function getDeserializedValue(
        JsonDeserializer $deserializer,
        $data,
        $value,
        string $deserializedClassName,
        string $prefixedFieldName
    ) {
        $value = (string)$value;

        $date = $this->isImmutable
            ? DateTimeImmutable::createFromFormat($this->formatString, $value, $this->timeZone)
            : DateTime::createFromFormat($this->formatString, $value, $this->timeZone);

        if (false === $date) {
            throw new TypeViolationException(
                $deserializedClassName,
                $prefixedFieldName,
                'DateTime',
                json_encode($value),
                $data
            );
        }

        return $date;
    }
}
