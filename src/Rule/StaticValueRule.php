<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Yapep\JsonDeserializer\JsonDeserializer;

class StaticValueRule implements IRule
{
    private $value;

    private bool $shouldValueBeSet;

    public function __construct($value, bool $shouldValueBeSet = true)
    {
        $this->value            = $value;
        $this->shouldValueBeSet = $shouldValueBeSet;
    }

    public function shouldValueBeSet($data): bool
    {
        return $this->shouldValueBeSet;
    }

    public function deserializeValue(
        JsonDeserializer $deserializer,
        $data,
        string $deserializedClassName,
        string $fieldPrefix = ''
    ) {
        return $this->value;
    }
}
