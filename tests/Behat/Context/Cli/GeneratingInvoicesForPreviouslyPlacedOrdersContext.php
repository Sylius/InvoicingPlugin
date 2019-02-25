<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Cli\CreateInvoicesForPreviouslyPlacedOrdersCommand;
use Sylius\InvoicingPlugin\Creator\InvoicesForPreviouslyPlacedOrdersCreatorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class GeneratingInvoicesForPreviouslyPlacedOrdersContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $tester;

    /** @var CreateInvoicesForPreviouslyPlacedOrdersCommand */
    private $command;

    /** @var InvoicesForPreviouslyPlacedOrdersCreatorInterface */
    private $invoicesForPreviouslyPlacedOrdersCreator;

    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        KernelInterface $kernel,
        InvoicesForPreviouslyPlacedOrdersCreatorInterface $invoicesForPreviouslyPlacedOrdersCreator,
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->kernel = $kernel;
        $this->invoicesForPreviouslyPlacedOrdersCreator = $invoicesForPreviouslyPlacedOrdersCreator;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given I generate invoices for previously placed orders
     */
    public function generateInvoicesForPreviouslyPlacedOrders(): void
    {
        $this->application = new Application($this->kernel);
        $this->application->add(
            new CreateInvoicesForPreviouslyPlacedOrdersCommand(
                $this->invoicesForPreviouslyPlacedOrdersCreator,
                $this->orderRepository
            )
        );

        $this->command = $this->application->find('sylius-invoicing:create-invoices-for-previously-placed-orders');
        $this->tester = new CommandTester($this->command);

        $this->tester->execute(['command' => 'sylius-invoicing:create-invoices-for-previously-placed-orders']);
    }
}
