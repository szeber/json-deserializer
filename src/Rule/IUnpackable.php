<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer\Rule;

interface IUnpackable
{
    public function isUnpacked(): bool;
}
