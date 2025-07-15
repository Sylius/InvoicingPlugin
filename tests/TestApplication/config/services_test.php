<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $env = $_ENV['APP_ENV'] ?? 'dev';

    if (str_starts_with($env, 'test')) {
        $container->import('../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services.xml');
        $container->import('@SyliusInvoicingPlugin/tests/Behat/Resources/services.xml');
    }

    if (filter_var($_ENV['TEST_SYLIUS_INVOICING_PDF_GENERATION_DISABLED'], FILTER_VALIDATE_BOOLEAN)) {
        $container->import('sylius_invoicing_pdf_generation_disabled.yaml');
    }
};
