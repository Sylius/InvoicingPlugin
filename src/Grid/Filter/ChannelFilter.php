<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class ChannelFilter implements FilterInterface
{
    /**
     * @param mixed $data
     * @param mixed[] $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if ($data['channel'] === '') {
            return;
        }

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('o.channel.code', $data['channel']));
    }
}
