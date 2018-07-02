<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function specifyCompany(string $company): void
    {
        $this->getElement('company')->setValue($company);
    }

    public function hasCompany(string $company): bool
    {
        return $this->getElement('company')->getValue() === $company;
    }

    public function specifyTaxId(string $taxId): void
    {
        $this->getElement('tax_id')->setValue($taxId);
    }

    public function hasTaxId(string $taxId): bool
    {
        return $this->getElement('tax_id')->getValue() === $taxId;
    }

    public function specifyBillingAddress(string $street, string $postcode, string $city, string $countryCode): void
    {
        $this->getElement('street')->setValue($street);
        $this->getElement('postcode')->setValue($postcode);
        $this->getElement('city')->setValue($city);
        $this->getElement('country_code')->setValue($countryCode);
    }

    public function hasBillingAddress(string $street, string $postcode, string $city, string $countryCode): bool
    {
        return
            $this->getElement('street')->getValue() === $street &&
            $this->getElement('postcode')->getValue() === $postcode &&
            $this->getElement('city')->getValue() === $city &&
            $this->getElement('country_code')->getValue() === $countryCode
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'city' => '#sylius_channel_billingData_city',
            'company' => '#sylius_channel_billingData_company',
            'country_code' => '#sylius_channel_billingData_countryCode',
            'postcode' => '#sylius_channel_billingData_postcode',
            'street' => '#sylius_channel_billingData_street',
            'tax_id' => '#sylius_channel_billingData_taxId',
        ]);
    }
}
