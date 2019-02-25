<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Cli;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\InvoicesForPreviouslyPlacedOrdersCreatorInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class CreateInvoicesForPreviouslyPlacedOrdersCommand extends Command
{
    /** @var InvoicesForPreviouslyPlacedOrdersCreatorInterface */
    private $invoiceForPreviouslyPlacedOrdersCreator;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        InvoicesForPreviouslyPlacedOrdersCreatorInterface $invoiceForPreviouslyPlacedOrdersCreator,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct('sylius-invoicing:create-invoices-for-previously-placed-orders');

        $this->invoiceForPreviouslyPlacedOrdersCreator = $invoiceForPreviouslyPlacedOrdersCreator;
        $this->orderRepository = $orderRepository;
    }

    protected function configure()
    {
        $this->setDescription('Generates invoices for orders placed before InvoicingPlugin installation');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var array $orders */
        $orders = $this->orderRepository->findAll();

        $this->invoiceForPreviouslyPlacedOrdersCreator->__invoke($orders);

        $output->writeln('Invoices generated successfully');
    }
}
