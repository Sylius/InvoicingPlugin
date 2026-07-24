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

use Tests\Sylius\InvoicingPlugin\Behat\Context\Cli\InvoicesGenerationContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Domain\GeneratingInvoiceContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Domain\InvoiceEmailContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Hook\InvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Order\OrderContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin\ManagingChannelsContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin\ManagingInvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop\AccountContext;
use Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop\CustomerBrowsingInvoicesContext;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel\UpdatePage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\ShowPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\ShowPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order\IndexPage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order\IndexPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\DownloadInvoicePage;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\DownloadInvoicePageInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.behat.page.admin.channel.update.class', UpdatePage::class);

    $services->set(IndexPageInterface::class, IndexPage::class)
        ->public()
        ->parent('sylius.behat.page.shop.account.order.index');

    $services->set(AccountContext::class)
        ->public()
        ->args([service(IndexPageInterface::class)]);

    $services->set(ManagingInvoicesContext::class)
        ->public()
        ->args([
            service(\Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPageInterface::class),
            service(ShowPageInterface::class),
            service(\Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPageInterface::class),
            service('sylius_invoicing.repository.invoice'),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set(CustomerBrowsingInvoicesContext::class)
        ->public()
        ->args([
            service(\Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPageInterface::class),
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

    $services->set(OrderContext::class)
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

    $services->set(\Tests\Sylius\InvoicingPlugin\Behat\Context\Setup\OrderContext::class)
        ->public()
        ->args([service('sylius.manager.order')]);

    $services->set(\Tests\Sylius\InvoicingPlugin\Behat\Context\Application\ManagingInvoicesContext::class)
        ->public()
        ->args([
            '%sylius_invoicing.invoice_save_path%',
            service('sylius_invoicing.repository.invoice'),
        ]);

    $services->set(\Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPageInterface::class, \Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPage::class)
        ->public()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_invoicing_admin_invoice_index']);

    $services->set(ShowPageInterface::class, ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')]);

    $services->set(DownloadInvoicePageInterface::class, DownloadInvoicePage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(\Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPageInterface::class, \Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(\Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPageInterface::class, \Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');
};
