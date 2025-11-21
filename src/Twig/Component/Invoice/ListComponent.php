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

namespace Sylius\InvoicingPlugin\Twig\Component\Invoice;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class ListComponent
{
    use HookableComponentTrait;

    public OrderInterface $order;

    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    /** @return array|InvoiceInterface[] */
    #[ExposeInTemplate('invoices')]
    public function getInvoices(): array
    {
        return $this->invoiceRepository->findByOrderNumber($this->order->getNumber());
    }
}
