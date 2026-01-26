<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor\ValueObject;

class ValidationResult
{
    /**
     * @var ValidationError[] $errors
     */
    private(set) array $errors = [];

    /**
     * @phpstan-assert-if-true non-empty-list<ValidationError> $this->errors
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function addError(string $path, string $message): void
    {
        $this->errors[] = new ValidationError($path, $message);
    }

    public function merge(self $validationResult): self
    {
        foreach ($validationResult->errors as $error) {
            $this->addError($error->path, $error->message);
        }

        return $this;
    }
}
