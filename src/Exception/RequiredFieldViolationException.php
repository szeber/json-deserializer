<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Exception;

class RequiredFieldViolationException extends DeserializerException
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
        $message = 'Required field ' . $fieldName . ' is missing in data while deserializing class '
            . $deserializedClass;

        parent::__construct($jsonData, $message, $code, $previous);
    }
}
