<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application\Entity\SyliusInvoicingPlugin;

use Doctrine\ORM\Mapping as ORM;
use Sylius\InvoicingPlugin\Entity\LineItem as BaseLineItem;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_invoicing_plugin_line_item')]
class LineItem extends BaseLineItem implements LineItemInterface
{
}
