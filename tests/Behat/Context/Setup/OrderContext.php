<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderContext implements Context
{
    private ObjectManager $orderManager;

    public function __construct(ObjectManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @Given /^(this order) has been placed in ("[^"]+" channel)$/
     */
    public function orderHasBeenPlacedInChannel(OrderInterface $order, ChannelInterface $channel): void
    {
        $order->setChannel($channel);

        $this->orderManager->flush();
    }

    /**
     * @Given /^(this order) has no number assigned$/
     */
    public function orderHasNoNumberAssigned(OrderInterface $order): void
    {
        $order->setNumber(null);

        $this->orderManager->flush();
    }
}
