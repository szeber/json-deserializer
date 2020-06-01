<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Exception;

class NullabilityViolationException extends DeserializerException
{
    /**
     * @param mixed $jsonData
     */
    public function __construct(
        string $deserializedClass,
        string $fieldName,
        $jsonData,
        int $code = 0,
        \Exception $previous = null
    ) {
        $message = 'Not nullable field ' . $fieldName . ' has a NULL value in the data while deserializing class '
            . $deserializedClass;

        parent::__construct($jsonData, $message, $code, $previous);
    }
}
