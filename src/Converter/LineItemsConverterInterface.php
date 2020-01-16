<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;

interface LineItemsConverterInterface
{
    /** @return Collection<array-key, LineItemInterface> */
    public function convert(OrderInterface $order): Collection;
}
