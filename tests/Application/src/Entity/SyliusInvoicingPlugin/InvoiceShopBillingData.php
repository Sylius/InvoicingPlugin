<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application\Entity\SyliusInvoicingPlugin;

use Doctrine\ORM\Mapping as ORM;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData as BaseInvoiceShopBillingData;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_invoicing_plugin_shop_billing_data")
 */
class InvoiceShopBillingData extends BaseInvoiceShopBillingData implements InvoiceShopBillingDataInterface
{
}
