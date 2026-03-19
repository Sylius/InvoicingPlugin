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

namespace Sylius\InvoicingPlugin\Twig;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InvoicesExtension extends AbstractExtension
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly bool $pdfEnabled = true,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('invoices_for_order', $this->invoicesForOrder(...)),
            new TwigFunction('invoices_pdf_enabled', $this->isPdfEnabled(...)),
        ];
    }

    public function invoicesForOrder(OrderInterface $order): array
    {
        return $this->invoiceRepository->findByOrderNumber($order->getNumber());
    }

    public function isPdfEnabled(): bool
    {
        return $this->pdfEnabled;
    }
}
