<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceNotAccessible;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceCustomerRelationChecker implements InvoiceCustomerRelationCheckerInterface
{
    /** @var CustomerContextInterface */
    private $customerContext;

    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        CustomerContextInterface $customerContext,
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->customerContext = $customerContext;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
    }

    public function check(string $invoiceId): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->get($invoiceId);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($invoice->orderNumber());

        /** @var CustomerInterface $orderCustomer */
        $orderCustomer = $order->getCustomer();

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        if ($orderCustomer->getId() !== $customer->getId()) {
            throw InvoiceNotAccessible::withUserId($invoiceId, $customer->getId());
        }
    }
}
