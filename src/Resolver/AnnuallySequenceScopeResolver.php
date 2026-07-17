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

namespace Sylius\InvoicingPlugin\Resolver;

use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;

final class AnnuallySequenceScopeResolver implements SequenceScopeResolverInterface
{
    public function supports(string $scope): bool
    {
        return InvoiceSequenceInterface::SCOPE_ANNUALLY === $scope;
    }

    public function resolve(\DateTimeImmutable $now): array
    {
        return ['year' => (int) $now->format('Y'), 'month' => 0];
    }
}
