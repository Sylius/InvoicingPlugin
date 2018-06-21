<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin;

final class SystemDateTimeProvider implements DateTimeProvider
{
    public function __invoke(): \DateTimeInterface
    {
        return new \DateTimeImmutable('now');
    }
}
