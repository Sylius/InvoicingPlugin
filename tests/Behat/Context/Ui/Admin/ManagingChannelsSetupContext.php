<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;

final class ManagingChannelsSetupContext implements Context
{
    /** @var UpdatePageInterface */
    private $updatePage;

    public function __construct(UpdatePageInterface $updatePage)
    {
        $this->updatePage = $updatePage;
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
        $this->updatePage->open(['id' => $channel->getId()]);
        $this->updatePage->specifyCompany($company);
        $this->updatePage->specifyTaxId($taxId);
        $this->updatePage->specifyBillingAddress($street, $postcode, $city, $country->getCode());
        $this->updatePage->saveChanges();
    }
}
