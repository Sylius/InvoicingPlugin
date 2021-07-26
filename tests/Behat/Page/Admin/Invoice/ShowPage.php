<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getIssuedAtDate(): \DateTimeInterface
    {
        // Jun 19, 2018, 2:17:29 PM

        return \DateTimeImmutable::createFromFormat('M j, Y, g:i:s A', $this->getElement('issued_at')->getText());
    }

    public function getRouteName(): string
    {
        return 'sylius_invoicing_plugin_admin_invoice_show';
    }

    public function hasBillingData(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName
    ): bool {
        $billingDataText = $this->getElement('billing_address')->getText();

        return
            (stripos($billingDataText, $customerName) !== false) &&
            (stripos($billingDataText, $street) !== false) &&
            (stripos($billingDataText, $city) !== false) &&
            (stripos($billingDataText, $countryName . ' ' . $postcode) !== false)
        ;
    }

    public function hasShopBillingData(
        string $company,
        string $taxId,
        string $countryName,
        string $street,
        string $city,
        string $postcode
    ): bool {
        $billingDataText = $this->getElement('shop_billing_data')->getText();

        return
            (stripos($billingDataText, $company) !== false) &&
            (stripos($billingDataText, $taxId) !== false) &&
            (stripos($billingDataText, $city) !== false) &&
            (stripos($billingDataText, $street) !== false) &&
            (stripos($billingDataText, $countryName . ' ' . $postcode) !== false)
        ;
    }

    public function countItems(): int
    {
        return count($this->getTableElements());
    }

    public function hasItemWithData(
        string $name,
        string $unitPrice,
        int $quantity,
        string $taxTotal,
        string $total,
        string $currencyCode = null,
        string $netValue = null
    ): bool {
        //todo $currencyCode $netValue
        foreach ($this->getTableElements() as $element) {
            if (
                ($element->find('css', '[data-test-line-item-name]')->getText() === $name ||
                $element->find('css', '[data-test-line-item-name]')->getText() === sprintf('%s (%s)', $name, $name)) &&
                $element->find('css', '[data-test-line-item-unit-price]')->getText() === $unitPrice &&
                $element->find('css', '[data-test-line-item-quantity]')->getText() === (string) $quantity &&
                $element->find('css', '[data-test-line-item-tax-total]')->getText() === $taxTotal &&
                $element->find('css', '[data-test-line-item-total]')->getText() === $total
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasTaxItem(string $label, string $amount,  string $currencyCode): bool
    {
        foreach ($this->getDocument()->findAll('css', '[data-test-invoice-tax-item]') as $item) {
            if ($item->find('css', '[data-test-invoice-tax-label]')->getText() === $label) {
                return
                    $item->find('css', '[data-test-invoice-tax-item-amount]')->getText() === $amount &&
                    $item->find('css', '[data-test-invoice-tax-item-currency]')->getText() === $currencyCode
                ;
            }
        }

        return false;
    }

    public function hasNetTotal(string $netTotal, string $currencyCode): bool
    {
        return
            $this->getDocument()->find('css', '[data-test-invoice-net-total]')->getText() === $netTotal &&
            $this->getDocument()->find('css', '[data-test-invoice-currency-code]')->getText() === $currencyCode
        ;
    }

    public function hasTaxTotal(string $taxTotal, string $currencyCode): bool
    {
        return
            $this->getDocument()->find('css', '[data-test-invoice-tax-total]')->getText() === $taxTotal &&
            $this->getDocument()->find('css', '[data-test-invoice-currency-code]')->getText() === $currencyCode
        ;
    }

    public function hasTotal(string $total, string $currencyCode): bool
    {
        return
            $this->getDocument()->find('css', '[data-test-invoice-total]')->getText() === $total &&
            $this->getDocument()->find('css', '[data-test-invoice-currency-code]')->getText() === $currencyCode
        ;
    }

    public function getSubtotal(): string
    {
        return str_replace('Subtotal: ', '', $this->getElement('invoice_subtotal')->getText());
    }

    public function getTotal(): string
    {
        return str_replace('Total: ', '', $this->getElement('invoice_total')->getText());
    }

    public function getChannel(): string
    {
        return $this->getElement('invoice_channel_name')->getText();
    }

    public function download(): void
    {
        $this->getDocument()->clickLink('Download');
    }

    public function resend(): void
    {
        $this->getDocument()->clickLink('Resend');
    }

    public function goBack(): void
    {
        $this->getElement('back')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'back' => '#back',
            'billing_address' => '#billing-data',
            'invoice_channel_name' => '#invoice-channel-name',
            'invoice_subtotal' => '[data-test-invoice-net-total]',
            'invoice_tax_total' => '#invoice-tax-total',
            'invoice_total' => '[data-test-invoice-total]',
            'issued_at' => '#invoice-issued-at',
            'shop_billing_data' => '#shop-billing-data',
        ]);
    }

    private function getTableElements(): array
    {
        return $this->getDocument()->findAll('css', '[data-test-line-item]');
    }
}
