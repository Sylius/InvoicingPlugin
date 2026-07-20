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
use Sylius\Component\Resource\Model\VersionedInterface;

interface InvoiceSequenceInterface extends ResourceInterface, VersionedInterface
{
    public const SCOPE_GLOBAL = 'global';

    public const SCOPE_MONTHLY = 'monthly';

    public const SCOPE_ANNUALLY = 'annually';

    public function getIndex(): int;

    public function incrementIndex(): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getYear(): int;

    public function setYear(int $year): void;

    public function getMonth(): int;

    public function setMonth(int $month): void;
}
