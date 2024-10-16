<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Order;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class OrderContext implements Context
{
    public function __construct(private ObjectManager $objectManager, private StateMachineInterface $stateMachine)
    {
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
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
        }
    }
}
