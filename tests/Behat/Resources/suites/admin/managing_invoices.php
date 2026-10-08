<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Application\ManagingInvoicesContext as ApplicationManagingInvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Cli\InvoicesGenerationContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Domain\GeneratingInvoiceContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Domain\InvoiceEmailContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Hook\InvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Order\OrderContext as OrderOrderContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Setup\OrderContext as SetupOrderContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin\ManagingChannelsContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin\ManagingInvoicesContext as AdminManagingInvoicesContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('managing_invoices_ui'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                InvoicesContext::class,
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.user',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.managing_orders',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.email',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.checkout',
                'sylius.behat.context.ui.shop.checkout.addressing',
                'sylius.behat.context.ui.shop.checkout.complete',
                'sylius.behat.context.ui.shop.currency',
            )
            ->withContexts(
                ManagingChannelsContext::class,
                AdminManagingInvoicesContext::class,
            )
            ->withContexts(
                InvoiceEmailContext::class,
                OrderOrderContext::class,
                ChannelContext::class,
                SetupOrderContext::class,
            )
            ->withFilter(new TagFilter('@managing_invoices&&@ui')),
        )
        ->withSuite(
            (new Suite('managing_invoices_application'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                InvoicesContext::class,
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
                ChannelContext::class,
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.user',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                ApplicationManagingInvoicesContext::class,
            )
            ->withFilter(new TagFilter('@managing_invoices&&@application')),
        )
        ->withSuite(
            (new Suite('managing_invoices_cli'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                InvoicesContext::class,
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.user',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                InvoiceEmailContext::class,
                OrderOrderContext::class,
                ChannelContext::class,
                SetupOrderContext::class,
            )
            ->withContexts(
                AdminManagingInvoicesContext::class,
            )
            ->withContexts(
                GeneratingInvoiceContext::class,
                InvoicesGenerationContext::class,
            )
            ->withFilter(new TagFilter('@managing_invoices&&@cli')),
        )
    )
;
