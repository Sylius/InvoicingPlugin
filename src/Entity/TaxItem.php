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

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class TaxItem implements TaxItemInterface, ResourceInterface
{
    /** @var mixed */
    protected $id;

    protected ?InvoiceInterface $invoice = null;

    public function __construct(
        protected string $label,
        protected int $amount,
    ) {
    }

    public function getId()
    {
        return $this->id();
    }

    public function id()
    {
        return $this->id;
    }

    public function invoice(): ?InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(InvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
