<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer;

use InvalidArgumentException;

class ProfileRegistry
{
    /** @var DeserializationProfile[] */
    private array $registeredProfiles = [];

    public function addProfile(string $className, DeserializationProfile $profile): self
    {
        $this->registeredProfiles[$className] = $profile;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getProfileForClass(string $className): DeserializationProfile
    {
        if (isset($this->registeredProfiles[$className])) {
            return $this->registeredProfiles[$className];
        }

        if (is_a($className, IJsonDeserializable::class, true)) {
            $profile = $className::getJsonDeserializationProfile();

            $this->addProfile($className, $profile);

            return $profile;
        }

        throw new InvalidArgumentException(
            'No JSON deserialization profile is set for class ' . $className . ' and it does not implement '
            . IJsonDeserializable::class
        );
    }

}
