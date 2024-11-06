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

namespace Sylius\InvoicingPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ShopBillingDataFixture extends AbstractResourceFixture
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'shop_billing_data';
    }

    /**
     * @inheritdoc
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('channel_code')->cannotBeEmpty()->end()
                ->scalarNode('company')->cannotBeEmpty()->end()
                ->scalarNode('country_code')->cannotBeEmpty()->end()
                ->scalarNode('city')->cannotBeEmpty()->end()
                ->scalarNode('postcode')->cannotBeEmpty()->end()
                ->scalarNode('tax_id')->cannotBeEmpty()->end()
                ->scalarNode('street_address')->cannotBeEmpty()->end()
        ;
    }
}
