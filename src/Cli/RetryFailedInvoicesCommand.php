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

namespace Sylius\InvoicingPlugin\Cli;

use Psr\Log\LoggerInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'sylius-invoicing:retry-failed-invoices',
    description: 'Retries sending invoice emails for invoices with missing PDF attachments.',
)]
final class RetryFailedInvoicesCommand extends Command
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly MessageBusInterface $commandBus,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $failedInvoices = $this->invoiceRepository->findUnsent();

        if ([] === $failedInvoices) {
            $output->writeln('No failed invoices found to retry.');

            return Command::SUCCESS;
        }

        $dispatched = 0;

        foreach ($failedInvoices as $invoice) {
            $orderNumber = $invoice->order()->getNumber();

            if (null === $orderNumber) {
                continue;
            }

            try {
                $this->commandBus->dispatch(new SendInvoiceEmail($orderNumber));
            } catch (ExceptionInterface $e) {
                $this->logger->error(
                    sprintf(
                        'Failed to dispatch invoice resend command for order %s: %s',
                        $orderNumber,
                        $e->getMessage(),
                    ),
                    ['exception' => $e],
                );
            }
            ++$dispatched;
        }

        $output->writeln(sprintf('Dispatched %d invoice resend command(s).', $dispatched));

        return Command::SUCCESS;
    }
}
