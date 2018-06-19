<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin;

interface DateTimeProvider
{
    public function __invoke(): \DateTimeInterface;
}
