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

namespace Sylius\InvoicingPlugin\Exception;

use function sprintf;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class InvoiceFileGenerationFailedException extends \RuntimeException
{
    public static function occur(): self
    {
        return new self('Invoice file cannot be generated.');
    }

    public static function forInvoice(InvoiceInterface $invoice, \Throwable $previous): self
    {
        return new self(
            sprintf(
                'Invoice file for invoice "%s" (%s) cannot be generated.',
                $invoice->number(),
                $invoice->id(),
            ),
            0,
            $previous,
        );
    }
}
