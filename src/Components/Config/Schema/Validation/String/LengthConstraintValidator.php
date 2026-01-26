<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Validation\String;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;

/**
 * @implements NodeConstraintValidatorInterface<string>
 */
readonly class LengthConstraintValidator implements NodeConstraintValidatorInterface
{
    public function __construct(
        private ?int $minLength,
        private ?int $maxLength,
    ) {
        if (null === $this->minLength && null === $this->maxLength) {
            throw new \LogicException('You must provide at least one min and max length');
        }
    }

    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        $stringLength = strlen($value);

        if (null !== $this->maxLength && null !== $this->minLength) {
            return $stringLength <= $this->maxLength && $stringLength >= $this->minLength;
        }

        if (null === $this->minLength) {
            return $stringLength <= $this->maxLength;
        }

        return $stringLength >= $this->minLength;
    }

    public function getMessage(): string
    {
        if (null !== $this->maxLength && null !== $this->minLength) {
            return sprintf(
                'The value length must be between %d and %d characters.',
                $this->minLength,
                $this->maxLength
            );
        }

        if (null === $this->minLength) {
            return sprintf(
                'The value length must be less than or equals to %d characters.',
                $this->maxLength
            );
        }

        return sprintf(
            'The value length must be greater than or equals to %d characters.',
            $this->minLength
        );
    }
}