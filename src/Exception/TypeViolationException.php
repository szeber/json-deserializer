<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Exception;

class TypeViolationException extends DeserializerException
{
    /**
     * @param mixed $jsonData
     */
    public function __construct(
        string $deserializedClass,
        string $fieldName,
        string $expectedType,
        string $jsonValue,
        $jsonData,
        int $code = 0,
        \Exception $previous = null
    ) {
        $message = 'Field ' . $fieldName . ' is expected to be of type ' . $expectedType . '. JSON value of `'
            . $jsonValue . '` found in data while deserializing class ' . $deserializedClass;

        parent::__construct($jsonData, $message, $code, $previous);
    }
}
