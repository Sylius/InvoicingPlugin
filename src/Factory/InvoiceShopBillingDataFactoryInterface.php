<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

interface InvoiceShopBillingDataFactoryInterface extends FactoryInterface
{
    public function createNew(): InvoiceShopBillingDataInterface;

    public function createFromChannel(ChannelInterface $channel): InvoiceShopBillingDataInterface;

    public function createFromShopBillingData(ShopBillingDataInterface $shopBillingData): InvoiceShopBillingDataInterface;
}