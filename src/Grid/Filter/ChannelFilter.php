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

namespace Sylius\InvoicingPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Webmozart\Assert\Assert;

final class ChannelFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        Assert::isArray($data);

        if (!array_key_exists('channel', $data) || $data['channel'] === '') {
            return;
        }

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('o.channel.code', $data['channel']));
    }
}
