<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application\Entity\SyliusInvoicingPlugin;

use Doctrine\ORM\Mapping as ORM;
use Sylius\InvoicingPlugin\Entity\InvoiceSequence as BaseInvoiceSequence;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_invoicing_plugin_sequence')]
class InvoiceSequence extends BaseInvoiceSequence implements InvoiceSequenceInterface
{
}
