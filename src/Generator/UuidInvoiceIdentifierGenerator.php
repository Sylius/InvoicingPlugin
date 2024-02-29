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

namespace Sylius\InvoicingPlugin\Generator;

use Ramsey\Uuid\Uuid;

final class UuidInvoiceIdentifierGenerator implements InvoiceIdentifierGenerator
{
    public function generate(): string
    {
        return Uuid::getFactory()->uuid4()->toString();
    }
}
