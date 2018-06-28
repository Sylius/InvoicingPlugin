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

        return
            (stripos($billingDataText, $customerName) !== false) &&
            (stripos($billingDataText, $street) !== false) &&
            (stripos($billingDataText, $city) !== false) &&
            (stripos($billingDataText, $countryName . ' ' . $postcode) !== false)
        ;
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

    public function getTaxTotal(): string
    {
        return $this->getElement('invoice_tax_total')->getText();
    }

    public function getTotal(): string
    {
        return $this->getElement('invoice_total')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '#billing-data',
            'invoice_tax_total' => '#invoice-tax-total',
            'invoice_total' => '#invoice-total',
            'issued_at' => '#invoice-issued-at',
            'table' => '.table',
        ]);
    }
}
