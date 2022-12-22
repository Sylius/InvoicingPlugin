<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application\Entity\SyliusInvoicingPlugin;

use Doctrine\ORM\Mapping as ORM;
use Sylius\InvoicingPlugin\Entity\Invoice as BaseInvoice;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_invoicing_plugin_invoice')]
class Invoice extends BaseInvoice implements InvoiceInterface
{
}
