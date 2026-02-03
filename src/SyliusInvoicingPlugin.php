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

namespace Sylius\InvoicingPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Telemetry\TelemetryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusInvoicingPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TelemetryCompilerPass());
    }
}
