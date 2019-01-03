<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceShopBillingDataSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('sample_company', '11001100', 'US', 'sample_street', 'sample_city', '11-111');
    }

    public function it_implements_invoice_shop_billing_data_interface(): void
    {
        $this->shouldImplement(InvoiceShopBillingDataInterface::class);
    }

    public function it_has_company_name(): void
    {
        $this->getCompany()->shouldReturn('sample_company');
    }

    public function it_has_tax_id(): void
    {
        $this->getTaxId()->shouldReturn('11001100');
    }

    public function it_has_country_code(): void
    {
        $this->getCountryCode()->shouldReturn('US');
    }

    public function it_has_street(): void
    {
        $this->getStreet()->shouldReturn('sample_street');
    }

    public function it_has_city(): void
    {
        $this->getCity()->shouldReturn('sample_city');
    }

    public function it_has_postcode(): void
    {
        $this->getPostcode()->shouldReturn('11-111');
    }
}
