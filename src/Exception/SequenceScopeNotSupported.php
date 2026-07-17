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

final class SequenceScopeNotSupported extends \InvalidArgumentException
{
    public static function withScope(string $scope): self
    {
        return new self(sprintf('No sequence scope resolver supports the "%s" scope', $scope));
    }
}
