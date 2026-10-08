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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop\AccountContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop\CustomerBrowsingInvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel\UpdatePage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPage as AdminInvoiceIndexPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPageInterface as AdminInvoiceIndexPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\ShowPage as AdminInvoiceShowPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\ShowPageInterface as AdminInvoiceShowPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPage as AdminOrderShowPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPageInterface as AdminOrderShowPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order\IndexPage as ShopAccountOrderIndexPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order\IndexPageInterface as ShopAccountOrderIndexPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\DownloadInvoicePage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\DownloadInvoicePageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPage as ShopOrderShowPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPageInterface as ShopOrderShowPageInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.behat.page.admin.channel.update.class', UpdatePage::class);

    $services->set(ShopAccountOrderIndexPageInterface::class, ShopAccountOrderIndexPage::class)
        ->public()
        ->parent('sylius.behat.page.shop.account.order.index');

    $services->set(AccountContext::class)
        ->public()
        ->args([service(ShopAccountOrderIndexPageInterface::class)]);

    $services->set(AdminManagingInvoicesContext::class)
        ->public()
        ->args([
            service(AdminInvoiceIndexPageInterface::class),
            service(AdminInvoiceShowPageInterface::class),
            service(AdminOrderShowPageInterface::class),
            service('sylius_invoicing.repository.invoice'),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set(CustomerBrowsingInvoicesContext::class)
        ->public()
        ->args([
            service(ShopOrderShowPageInterface::class),
            service(DownloadInvoicePageInterface::class),
            service('sylius_invoicing.repository.invoice'),
        ]);

    $services->set(InvoiceEmailContext::class)
        ->public()
        ->args([service('sylius.behat.email_checker')]);

    $services->set(GeneratingInvoiceContext::class)
        ->public()
        ->args([
            service('sylius_invoicing.manager.invoice'),
            service('sylius_invoicing.repository.invoice'),
        ]);

    $services->set(InvoicesContext::class)
        ->public()
        ->args(['%sylius_invoicing.invoice_save_path%']);

    $services->set(InvoicesGenerationContext::class)
        ->public()
        ->args([
            service('kernel'),
            service('sylius_invoicing.creator.mass_invoices'),
            service('sylius_invoicing.repository.invoice'),
            service('sylius.repository.order'),
        ]);

    $services->set(OrderOrderContext::class)
        ->public()
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set(ManagingChannelsContext::class)
        ->public()
        ->args([service('sylius.behat.page.admin.channel.update')]);

    $services->set(ChannelContext::class)
        ->public()
        ->args([service('sylius.manager.channel')]);

    $services->set(SetupOrderContext::class)
        ->public()
        ->args([service('sylius.manager.order')]);

    $services->set(ApplicationManagingInvoicesContext::class)
        ->public()
        ->args([
            '%sylius_invoicing.invoice_save_path%',
            service('sylius_invoicing.repository.invoice'),
        ]);

    $services->set(AdminInvoiceIndexPageInterface::class, AdminInvoiceIndexPage::class)
        ->public()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_invoicing_admin_invoice_index']);

    $services->set(AdminInvoiceShowPageInterface::class, AdminInvoiceShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')]);

    $services->set(DownloadInvoicePageInterface::class, DownloadInvoicePage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(AdminOrderShowPageInterface::class, AdminOrderShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(ShopOrderShowPageInterface::class, ShopOrderShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');
};
