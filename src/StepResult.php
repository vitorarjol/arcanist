<?php declare(strict_types=1);

namespace Arcanist;

final class StepResult
{
    private function __construct(
        private bool $successful,
        private array $payload = [],
        private ?string $error = null
    ) {
    }

    public static function success(array $payload = []): self
    {
        return new self(true, payload: $payload);
    }

    public static function failed(?string $error = null): self
    {
        return new self(false, error: $error);
    }

    public function successful(): bool
    {
        return $this->successful;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function error(): ?string
    {
        return $this->error;
    }
}
