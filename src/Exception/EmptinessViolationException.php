<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Exception;

class EmptinessViolationException extends DeserializerException
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
        $message = 'Field ' . $fieldName . ' is expected to be not empty, but the JSON value of `'
            . $jsonValue . '` is considered empty for type ' . $expectedType . 'while deserializing class '
            . $deserializedClass;

        parent::__construct($jsonData, $message, $code, $previous);
    }
}
