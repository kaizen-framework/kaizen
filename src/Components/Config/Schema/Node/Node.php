<?php

declare(strict_types=1);

namespace App\Components\Config\Schema\Node;

use App\Components\Config\Exception\ConfigProcessingException;

abstract class Node implements NodeInterface
{
    private bool $isRequired = false;
    private mixed $defaultValue = null;

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function defaultValue(mixed $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function processValue(mixed $value): mixed
    {
        if (null === $value && null !== $this->defaultValue) {
            $value = $this->defaultValue;
        }

        if (null === $value && $this->isRequired) {
            throw new ConfigProcessingException(sprintf(
                'The node "%s" is required, but null provided.',
                $this->getKey()
            ));
        }

        return $value;
    }
}