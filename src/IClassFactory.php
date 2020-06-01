<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer;

interface IClassFactory
{
    public function createClass(
        JsonDeserializer $deserializer,
        array $data,
        string $className,
        string $fieldPrefix,
        DeserializationProfile $deserializationProfile
    ): object;
}
