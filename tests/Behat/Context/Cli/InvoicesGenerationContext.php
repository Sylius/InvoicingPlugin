<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Cli\GenerateInvoicesCommand;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreatorInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class InvoicesGenerationContext implements Context
{
    private KernelInterface $kernel;

    private MassInvoicesCreatorInterface $massInvoicesCreator;

    private InvoiceRepositoryInterface $invoiceRepository;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        KernelInterface $kernel,
        MassInvoicesCreatorInterface $massInvoicesCreator,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->kernel = $kernel;
        $this->massInvoicesCreator = $massInvoicesCreator;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given I generate invoices for previously placed orders
     */
    public function generateInvoicesForPreviouslyPlacedOrders(): void
    {
        /** @var Application $application */
        $application = new Application($this->kernel);
        $application->add(
            new GenerateInvoicesCommand(
                $this->massInvoicesCreator,
                $this->orderRepository
            )
        );

        /** @var Command $command */
        $command = $application->find('sylius-invoicing:generate-invoices');

        /** @var CommandTester $tester */
        $tester = new CommandTester($command);

        $tester->execute(['command' => 'sylius-invoicing:generate-invoices']);
    }
}
