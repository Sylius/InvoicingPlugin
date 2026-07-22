<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\InvoicingPlugin\Converter\OrderItemUnitsToLineItemsConverter;
use Sylius\InvoicingPlugin\Converter\ShippingAdjustmentsToLineItemsConverter;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverter;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_invoicing.converter.order_item_units_to_line_items', OrderItemUnitsToLineItemsConverter::class)
        ->args([
            service('sylius_invoicing.provider.tax_rate_percentage'),
            service('sylius_invoicing.factory.line_item'),
            service('sylius_invoicing.provider.unit_net_price'),
        ]);

    $services->set('sylius_invoicing.converter.shipping_adjustments_to_line_items', ShippingAdjustmentsToLineItemsConverter::class)
        ->args([
            service('sylius_invoicing.provider.tax_rate_percentage'),
            service('sylius_invoicing.factory.line_item'),
        ]);

    $services->set('sylius_invoicing.converter.tax_items', TaxItemsConverter::class)
        ->args([
            service('sylius_invoicing.provider.tax_rate_percentage'),
            service('sylius_invoicing.factory.tax_item'),
        ]);

    $services->alias(TaxItemsConverterInterface::class, 'sylius_invoicing.converter.tax_items');
};
