<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer;

use InvalidArgumentException;
use JsonException;
use Yapep\JsonDeserializer\Exception\DeserializerException;
use Yapep\JsonDeserializer\Rule\IUnpackable;

class JsonDeserializer
{
    private ProfileRegistry $profileRegistry;

    public function __construct(ProfileRegistry $profileRegistry)
    {
        $this->profileRegistry = $profileRegistry;
    }

    /**
     * @throws InvalidArgumentException
     * @throws DeserializerException
     * @throws JsonException
     */
    public function deserializeToClassFromJson(string $className, string $jsonData, string $fieldPrefix = ''): object
    {
        $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new InvalidArgumentException('Failed to decode the json to an array: ' . $jsonData);
        }

        return $this->deserializeToClassFromArray($className, $data, $fieldPrefix);
    }

    /**
     * @throws InvalidArgumentException
     * @throws DeserializerException
     */
    public function deserializeToClassFromArray(string $className, array $data, string $fieldPrefix = ''): object
    {
        $profile = $this->profileRegistry->getProfileForClass($className);

        $instance = $this->createClass($className, $profile, $data, $fieldPrefix);

        foreach ($profile->getPropertyRules() as $propertyName => $rule) {
            if (!$rule->shouldValueBeSet($data)) {
                continue;
            }

            $instance->$propertyName = $rule->deserializeValue($this, $data, $className, $fieldPrefix);
        }

        foreach ($profile->getSetterRules() as $setterName => $rule) {
            if (!$rule->shouldValueBeSet($data)) {
                continue;
            }

            if ($rule instanceof IUnpackable && $rule->isUnpacked()) {
                $instance->$setterName(...$rule->deserializeValue($this, $data, $className, $fieldPrefix));
            } else {
                $instance->$setterName($rule->deserializeValue($this, $data, $className, $fieldPrefix));
            }

        }

        foreach ($profile->getCallables() as $callable) {
            $callable($this, $data, $instance, $fieldPrefix);
        }

        return $instance;
    }

    /**
     * @throws DeserializerException
     */
    protected function createClass(
        string $className,
        DeserializationProfile $profile,
        array $data,
        string $fieldPrefix
    ): object {
        $classFactory = $profile->getClassFactory();

        if (null !== $classFactory) {
            return $classFactory->createClass($this, $data, $className, $fieldPrefix, $profile);
        }

        $parameters = [];

        foreach ($profile->getConstructorRules() as $rule) {
            if ($rule instanceof IUnpackable && $rule->isUnpacked()) {
                $parameters = array_merge(
                    $parameters,
                    array_values($rule->deserializeValue($this, $data, $className, $fieldPrefix))
                );
            } else {
                $parameters[] = $rule->deserializeValue($this, $data, $className, $fieldPrefix);
            }
        }

        return new $className(...$parameters);
    }
}
