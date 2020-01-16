<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use Sylius\Component\Core\Model\OrderInterface;

interface MassInvoicesCreatorInterface
{
    /** @param OrderInterface[] $orders */
    public function __invoke(array $orders): void;
}
