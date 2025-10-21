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

namespace Sylius\InvoicingPlugin\Enum;

enum InvoiceSequenceScopeEnum: string
{
    case GLOBAL = 'global';
    case MONTHLY = 'monthly';
    case ANNUALLY = 'annually';

    public static function fromString(?string $value): self
    {
        return match ($value) {
            'monthly' => self::MONTHLY,
            'annually' => self::ANNUALLY,
            default => self::GLOBAL,
        };
    }
}
