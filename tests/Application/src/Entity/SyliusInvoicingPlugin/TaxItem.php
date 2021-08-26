<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application\Entity\SyliusInvoicingPlugin;

use Doctrine\ORM\Mapping as ORM;
use Sylius\InvoicingPlugin\Entity\TaxItem as BaseTaxItem;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_invoicing_plugin_tax_item")
 */
class TaxItem extends BaseTaxItem implements TaxItemInterface
{
}
