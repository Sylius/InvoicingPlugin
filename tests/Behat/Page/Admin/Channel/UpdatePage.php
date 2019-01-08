<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function specifyCompany(string $company): void
    {
        $this->getElement('company')->setValue($company);
    }

    public function hasCompany(string $company): bool
    {
        return $company === $this->getElement('company')->getValue();
    }

    public function specifyTaxId(string $taxId): void
    {
        $this->getElement('tax_id')->setValue($taxId);
    }

    public function hasTaxId(string $taxId): bool
    {
        return $taxId === $this->getElement('tax_id')->getValue();
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
            $street === $this->getElement('street')->getValue() &&
            $postcode === $this->getElement('postcode')->getValue() &&
            $city === $this->getElement('city')->getValue() &&
            $countryCode === $this->getElement('country_code')->getValue()
        ;
    }

    public function hasValidationError(string $message): bool
    {
        $validationMessages = $this->getSession()->getPage()->findAll('css', '.sylius-validation-error');

        if (empty($validationMessages)) {
            return false;
        }

        /** @var NodeElement $validationMessage */
        foreach ($validationMessages as $validationMessage) {
            if ($validationMessage->getText() === $message) {
                return true;
            }
        }

        return false;
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
            'validation_error' => '.sylius-validation-error',
        ]);
    }
}
