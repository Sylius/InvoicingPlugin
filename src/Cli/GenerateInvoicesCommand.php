<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Cli;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateInvoicesCommand extends Command
{
    /** @var MassInvoicesCreatorInterface */
    private $massInvoicesCreator;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        MassInvoicesCreatorInterface $massInvoicesCreator,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct('sylius-invoicing:generate-invoices');

        $this->massInvoicesCreator = $massInvoicesCreator;
        $this->orderRepository = $orderRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<OrderInterface> $orders */
        $orders = $this->orderRepository
            ->createListQueryBuilder()
            ->andWhere('o.number IS NOT NULL')
            ->getQuery()
            ->getResult();

        $this->massInvoicesCreator->__invoke($orders);

        $output->writeln('Invoices generated successfully');

        return 0;
    }

    protected function configure(): void
    {
        $this->setDescription('Generates invoices for orders placed before InvoicingPlugin installation');
    }
}
