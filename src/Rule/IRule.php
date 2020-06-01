<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

use Yapep\JsonDeserializer\Exception\DeserializerException;
use Yapep\JsonDeserializer\JsonDeserializer;

interface IRule
{

    /**
     * @param mixed $data
     */
    public function shouldValueBeSet($data): bool;

    /**
     * @param mixed $data
     *
     * @throws DeserializerException
     */
    public function deserializeValue(
        JsonDeserializer $deserializer,
        $data,
        string $deserializedClassName,
        string $fieldPrefix = ''
    );
}
