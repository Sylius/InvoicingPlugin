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

use Sylius\InvoicingPlugin\Fixture\Factory\ShopBillingDataExampleFactory;
use Sylius\InvoicingPlugin\Fixture\Listener\InvoicesPurgerListener;
use Sylius\InvoicingPlugin\Fixture\ShopBillingDataFixture;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_invoicing.fixture.example_factory.shop_billing_data', ShopBillingDataExampleFactory::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.factory.shop_billing_data'),
        ]);

    $services->set('sylius_invoicing.fixture.shop_billing_data', ShopBillingDataFixture::class)
        ->args([
            service('sylius.manager.channel'),
            service('sylius_invoicing.fixture.example_factory.shop_billing_data'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius_invoicing.fixture.listener.invoices_purger', InvoicesPurgerListener::class)
        ->args([
            service('filesystem'),
            '%sylius_invoicing.invoice_save_path%',
        ])
        ->tag('sylius_fixtures.listener');
};
