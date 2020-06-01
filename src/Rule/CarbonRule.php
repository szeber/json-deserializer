<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeZone;
use Yapep\JsonDeserializer\Exception\TypeViolationException;
use Yapep\JsonDeserializer\JsonDeserializer;

class CarbonRule extends RuleAbstract
{
    private bool $isImmutable;

    private string $formatString;

    private ?DateTimeZone $timeZone;

    public function __construct(
        string $fieldName,
        bool $isRequired,
        bool $isNullable,
        bool $isImmutable = false,
        string $formatString = CarbonInterface::ATOM,
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

        try {
            return $this->isImmutable
                ? CarbonImmutable::createFromFormat($this->formatString, $value, $this->timeZone)
                : Carbon::createFromFormat($this->formatString, $value, $this->timeZone);
        } catch (\Exception $e) {
            throw new TypeViolationException(
                $deserializedClassName,
                $prefixedFieldName,
                'Carbon',
                json_encode($value),
                $data
            );
        }
    }
}
