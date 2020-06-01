<?php
declare(strict_types=1);

namespace Yapep\JsonDeserializer;

use Yapep\JsonDeserializer\Rule\IRule;

class DeserializationProfile
{
    /** @var IRule[] */
    private array $constructorRules = [];

    /** @var IRule[] */
    private array $propertyRules = [];

    /** @var IRule[] */
    private array $setterRules = [];

    /** @var callable[] */
    private array $callables = [];

    private ?IClassFactory $classFactory;

    public function __construct(?IClassFactory $classFactory = null)
    {
        $this->setClassFactory($classFactory);
    }

    public  function setClassFactory(?IClassFactory $classFactory): self
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    public function addConstructorRule(IRule $rule): self
    {
        $this->constructorRules[] = $rule;

        return $this;
    }

    public function addPropertyRule(string $propertyName, IRule $rule): self
    {
        $this->propertyRules[$propertyName] = $rule;

        return $this;
    }

    public function addSetterRule(string $setterMethod, IRule $rule): self
    {
        $this->setterRules[$setterMethod] = $rule;

        return $this;
    }

    public function addCallable(callable $callable): self
    {
        $this->callables[] = $callable;

        return $this;
    }

    /**
     * @return IRule[]
     */
    public function getConstructorRules(): array
    {
        return $this->constructorRules;
    }

    /**
     * @return IRule[]
     */
    public function getPropertyRules(): array
    {
        return $this->propertyRules;
    }

    /**
     * @return IRule[]
     */
    public function getSetterRules(): array
    {
        return $this->setterRules;
    }

    /**
     * @return callable[]
     */
    public function getCallables(): array
    {
        return $this->callables;
    }

    public function getClassFactory(): ?IClassFactory
    {
        return $this->classFactory;
    }

}
