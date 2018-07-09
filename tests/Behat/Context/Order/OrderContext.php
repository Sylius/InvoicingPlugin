<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Order;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class OrderContext implements Context
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    public function __construct(ObjectManager $objectManager, StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->objectManager = $objectManager;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * @When the order :order has just been paid
     */
    public function orderHasJustBeenPaid(OrderInterface $order): void
    {
        $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    private function applyPaymentTransitionOnOrder(OrderInterface $order, $transition): void
    {
        foreach ($order->getPayments() as $payment) {
            $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply($transition);
        }
    }
}
