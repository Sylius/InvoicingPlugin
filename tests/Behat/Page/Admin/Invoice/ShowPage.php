<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        array $parameters,
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

        return $this->doesTextContainPhrases(
            $billingDataText,
            $customerName,
            $street,
            $city,
            $countryName . ' ' . $postcode
        );
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

        return $this->doesTextContainPhrases(
            $billingDataText,
            $company,
            $taxId,
            $street,
            $city,
            $countryName . ' ' . $postcode
        );
    }

    public function countItems(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('table'));
    }

    public function hasItemWithData(
        string $name,
        string $unitPrice,
        int $quantity,
        string $taxTotal,
        string $total
    ): bool {
        $row = $this->tableAccessor->getRowsWithFields($this->getElement('table'), [
            'name' => $name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'tax_total' => $taxTotal,
            'total' => $total,
        ])[0];

        return null !== $row;
    }

    public function hasTaxItem(string $label, string $amount): bool
    {
        $taxItemAmountElement = $this->getElement('tax_item_amount', ['%label%' => $label]);

        return $amount === $taxItemAmountElement->getText();
    }

    public function getSubtotal(): string
    {
        return $this->getElement('invoice_subtotal')->getText();
    }

    public function getTotal(): string
    {
        return $this->getElement('invoice_total')->getText();
    }

    public function download(): void
    {
        $this->getDocument()->clickLink('Download');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '#billing-data',
            'shop_billing_data' => '#shop-billing-data',
            'invoice_tax_total' => '#invoice-tax-total',
            'invoice_subtotal' => '#invoice-subtotal',
            'invoice_total' => '#invoice-total',
            'issued_at' => '#invoice-issued-at',
            'table' => '.table',
            'tax_item_amount' => 'tr.tax-item:contains("%label%") .tax-item-amount',
        ]);
    }

    private function doesTextContainPhrases(string $text, string...$phrases): bool
    {
        foreach ($phrases as $phrase) {
            if (!stripos($billingDataText, $company)) {
                return false;
            }
        }

        return true;
    }
}
