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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface InvoiceInterface extends ResourceInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    public function id(): string;

    public function number(): string;

    public function order(): OrderInterface;

    public function issuedAt(): \DateTimeInterface;

    public function billingData(): BillingDataInterface;

    public function currencyCode(): string;

    public function localeCode(): string;

    public function total(): int;

    public function lineItems(): Collection;

    public function taxItems(): Collection;

    public function subtotal(): int;

    public function taxesTotal(): int;

    public function channel(): ChannelInterface;

    public function shopBillingData(): InvoiceShopBillingDataInterface;

    public function paymentState(): string;
}
