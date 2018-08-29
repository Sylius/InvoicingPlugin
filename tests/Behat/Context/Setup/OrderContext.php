<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderContext implements Context
{
    /** @var ObjectManager */
    private $orderManager;

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
}
