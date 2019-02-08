<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShopBillingDataExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ChannelInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($options['channel_code']);
        if ($channel === null) {
            throw new ChannelNotFoundException(sprintf('Channel %s has not been found, please create it before adding this fixture !', $options['code']));
        }

        $billingData = new ShopBillingData();
        $billingData->setCompany($options['company']);
        $billingData->setCountryCode($options['country_code']);
        $billingData->setCity($options['city']);
        $billingData->setPostcode($options['postcode']);
        $billingData->setTaxId($options['tax_id']);
        $billingData->setStreet($options['street_address']);

        $channel->setShopBillingData($billingData);

        return $channel;
    }

    /**
     * {@inheritdoc}
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
