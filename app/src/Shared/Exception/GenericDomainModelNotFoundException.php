<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(404)]
class GenericDomainModelNotFoundException extends \RuntimeException
{
    final private function __construct(
        public string|int $id,
        public string $model,
        string $message,
    ) {
        parent::__construct(str_replace(['%model%', '%id%'], [$this->model, $this->id], $message));
    }

    final public static function withId(
        string|int $id,
        string $model,
        string $message = '%model% with id "%id%" not found.',
    ): static {
        return new static(id: $id, model: $model, message: $message);
    }
}
