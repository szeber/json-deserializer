<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Exception;

use RuntimeException;

class DeserializerException extends RuntimeException
{
    protected array $jsonData;

    /**
     * @param mixed $jsonData
     */
    public function __construct($jsonData, string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->jsonData = $jsonData;
    }

    /**
     * @return mixed
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

}
