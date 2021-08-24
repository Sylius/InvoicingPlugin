<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
