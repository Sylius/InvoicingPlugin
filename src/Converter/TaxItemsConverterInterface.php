<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;

interface TaxItemsConverterInterface
{
    public function convert(OrderInterface $order): Collection;
}
