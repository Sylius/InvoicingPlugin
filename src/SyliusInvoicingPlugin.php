<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusInvoicingPlugin extends Bundle
{
    use SyliusPluginTrait;
}
