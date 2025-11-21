<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    private TableAccessorInterface $tableAccessor;

    public function __construct(
        Session $session,
        $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    public function getIssuedAtDate(): \DateTimeInterface
    {
        // Jun 19, 2018, 2:17:29 PM

        return \DateTimeImmutable::createFromFormat('M j, Y, g:i:s A', $this->getElement('issued_at')->getText());
    }

    public function getRouteName(): string
    {
        return 'sylius_invoicing_admin_invoice_show';
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
        return $this->tableAccessor->countTableBodyRows($this->getElement('table'));
    }

    public function hasItemWithData(
        string $name,
        string $unitNetPrice,
        string $discountedUnitNetPrice,
        int $quantity,
        string $taxTotal,
        string $total,
        string $currencyCode = null,
        string $netValue = null
    ): bool {
        $row = $this->tableAccessor->getRowsWithFields($this->getElement('table'), [
            'name' => $name,
            'unit-net-price' => $unitNetPrice,
            'discounted-unit-net-price' => $discountedUnitNetPrice,
            'quantity' => $quantity,
            'tax_total' => $taxTotal,
            'total' => $total,
        ])[0];

        return null !== $row;
    }

    public function hasTaxItem(string $label, string $amount,  string $currencyCode): bool
    {
        foreach ($this->getDocument()->findAll('css', '[data-test-invoice-tax-item]') as $item) {
            if (
                $item->find('css', '[data-test-invoice-tax-item-label]')->getText() === $label &&
                $item->find('css', '[data-test-invoice-tax-item-amount]')->getText() === $amount &&
                $item->find('css', '[data-test-invoice-tax-item-currency-code]')->getText() === $currencyCode
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasNetTotal(string $netTotal, string $currencyCode): bool
    {
        return
            $this->getElement('invoice_net_total')->getText() === $netTotal &&
            $this->getElement('invoice_net_total_currency_code')->getText() === $currencyCode
        ;
    }

    public function hasTaxTotal(string $taxTotal, string $currencyCode): bool
    {
        return
            $this->getElement('invoice_taxes_total')->getText() === $taxTotal &&
            $this->getElement('invoice_taxes_total_currency_code')->getText() === $currencyCode
        ;
    }

    public function hasTotal(string $total, string $currencyCode): bool
    {
        return
            $this->getElement('invoice_total')->getText() === $total &&
            $this->getElement('invoice_total_currency_code')->getText() === $currencyCode
        ;
    }

    public function getChannel(): string
    {
        return $this->getElement('channel')->getText();
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

    public function isPaid(): bool
    {
        return str_contains($this->getElement('paid')->getHtml(), 'Yes');
    }

    public function hasDownloadButton(): bool
    {
        return $this->getDocument()->hasLink('Download');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'back' => '[data-test-back]',
            'billing_address' => '[data-test-billing-data]',
            'channel' => '[data-test-channel]',
            'invoice_net_total' => '[data-test-invoice-net-total]',
            'invoice_net_total_currency_code' => '[data-test-invoice-net-total-currency-code]',
            'invoice_taxes_total' => '[data-test-invoice-taxes-total]',
            'invoice_taxes_total_currency_code' => '[data-test-invoice-taxes-total-currency-code]',
            'invoice_total' => '[data-test-invoice-total]',
            'invoice_total_currency_code' => '[data-test-invoice-total-currency-code]',
            'issued_at' => '[data-test-issued-at]',
            'paid' => '[data-test-invoice-is-paid]',
            'shop_billing_data' => '[data-test-shop-billing-data]',
            'table' => '.table',
        ]);
    }

    private function getTableElements(): array
    {
        return $this->getDocument()->findAll('css', '[data-test-line-item]');
    }
}
