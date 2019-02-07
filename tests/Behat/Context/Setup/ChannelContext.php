<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\InvoicingPlugin\Entity\ShopBillingDataAwareInterface;

final class ChannelContext implements Context
{
    /** @var ObjectManager */
    private $channelManager;

    public function __construct(ObjectManager $channelManager)
    {
        $this->channelManager = $channelManager;
    }

    /**
     * @Given I set shop billing data for channel :channel as :company, :taxId, :street, :postcode :city, :country
     */
    public function setShopBillingDataForChannel(
        ChannelInterface $channel,
        string $company,
        string $taxId,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        $shopBillingData = new ShopBillingData();
        $shopBillingData->setCity($city);
        $shopBillingData->setCompany($company);
        $shopBillingData->setPostcode($postcode);
        $shopBillingData->setStreet($street);
        $shopBillingData->setCountryCode($country->getCode());
        $shopBillingData->setTaxId($taxId);

        $channel->setShopBillingData($shopBillingData);

        $this->channelManager->flush();
    }
}
