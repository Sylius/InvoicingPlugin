<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceShopBillingDataSpec extends ObjectBehavior
{
    public function it_implements_invoice_shop_billing_data_interface(): void
    {
        $this->shouldImplement(InvoiceShopBillingDataInterface::class);
    }

    public function it_has_company_name(): void
    {
        $this->setCompany('sample_company');

        $this->getCompany()->shouldReturn('sample_company');
    }

    public function it_has_tax_id(): void
    {
        $this->setTaxId('11001100');

        $this->getTaxId()->shouldReturn('11001100');
    }

    public function it_has_country_code(): void
    {
        $this->setCountryCode('US');

        $this->getCountryCode()->shouldReturn('US');
    }

    public function it_has_street(): void
    {
        $this->setStreet('sample_street');

        $this->getStreet()->shouldReturn('sample_street');
    }

    public function it_has_city(): void
    {
        $this->setCity('sample_city');

        $this->getCity()->shouldReturn('sample_city');
    }

    public function it_has_postcode(): void
    {
        $this->setPostcode('11-111');

        $this->getPostcode()->shouldReturn('11-111');
    }
}
