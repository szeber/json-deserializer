<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer;

interface IJsonDeserializable
{
    public static function getJsonDeserializationProfile(): DeserializationProfile;
}
