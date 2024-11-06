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

namespace Sylius\InvoicingPlugin\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShopBillingDataExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private readonly OptionsResolver $optionsResolver;

    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly FactoryInterface $factory,
    ) {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @inheritdoc
     */
    public function create(array $options = []): ChannelInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($options['channel_code']);
        if ($channel === null) {
            throw new ChannelNotFoundException(sprintf('Channel %s has not been found, please create it before adding this fixture !', $options['code']));
        }

        /** @var ShopBillingData $shopBillingData */
        $shopBillingData = $this->factory->createNew();
        $shopBillingData->setCompany($options['company']);
        $shopBillingData->setCountryCode($options['country_code']);
        $shopBillingData->setCity($options['city']);
        $shopBillingData->setPostcode($options['postcode']);
        $shopBillingData->setTaxId($options['tax_id']);
        $shopBillingData->setStreet($options['street_address']);

        $channel->setShopBillingData($shopBillingData);

        return $channel;
    }

    /**
     * @inheritdoc
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('channel_code')
            ->setAllowedTypes('channel_code', 'string')
            ->setRequired('company')
            ->setAllowedTypes('company', 'string')
            ->setRequired('country_code')
            ->setAllowedTypes('country_code', 'string')
            ->setRequired('city')
            ->setAllowedTypes('city', 'string')
            ->setRequired('postcode')
            ->setAllowedTypes('postcode', 'string')
            ->setRequired('tax_id')
            ->setAllowedTypes('tax_id', 'string')
            ->setRequired('street_address')
            ->setAllowedTypes('street_address', 'string')
        ;
    }
}
