<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;

interface LineItemsConverterInterface
{
    /** @return LineItemInterface[] */
    public function convert(OrderInterface $order): array;
}
