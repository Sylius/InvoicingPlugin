<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\CountryInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    /** @var UpdatePageInterface */
    private $updatePage;

    public function __construct(UpdatePageInterface $updatePage)
    {
        $this->updatePage = $updatePage;
    }

    /**
     * @When I specify company as :company
     */
    public function specifyCompanyAs(string $company): void
    {
        $this->updatePage->specifyCompany($company);
    }

    /**
     * @When I specify tax ID as :taxId
     */
    public function specifyTaxIdAs(string $taxId): void
    {
        $this->updatePage->specifyTaxId($taxId);
    }

    /**
     * @When I specify shop billing address as :street, :postcode :city, :country
     */
    public function specifyShopBillingAddressAs(
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        $this->updatePage->specifyBillingAddress($street, $postcode, $city, $country->getCode());
    }

    /**
     * @Then this channel company should be :company
     */
    public function thisChannelCompanyShouldBe(string $company): void
    {
        Assert::true($this->updatePage->hasCompany($company));
    }

    /**
     * @Then this channel tax ID should be :taxId
     */
    public function thisChanneTaxIdShouldBe(string $taxId): void
    {
        Assert::true($this->updatePage->hasTaxId($taxId));
    }

    /**
     * @Then this channel shop billing address should be :street, :postcode :city, :country
     */
    public function thisChannelShopBillingAddressShouldBe(
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        Assert::true($this->updatePage->hasBillingAddress($street, $postcode, $city, $country->getCode()));
    }

    /**
     * @Then I should be notified that :input cannot be blank
     */
    public function shouldBeNotifiedThatCannotBeBlank(string $input): void
    {
        Assert::true($this->updatePage->hasValidationError($input . ' cannot be blank'));
    }
}
