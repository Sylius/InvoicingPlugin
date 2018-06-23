<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;

final class InvoiceSpec extends ObjectBehavior
{
    function let(
        BillingDataInterface $billingData,
        LineItemInterface $lineItem
    ): void {
        $issuedAt = new \DateTimeImmutable('now');

        $this->beConstructedWith(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '007',
            $issuedAt,
            $billingData,
            'USD',
            300,
            10300,
            new ArrayCollection([$lineItem->getWrappedObject()])
        );
    }

    function it_implements_invoice_interface(): void
    {
        $this->shouldImplement(InvoiceInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_an_id(
        BillingDataInterface $billingData,
        LineItemInterface $lineItem
    ): void {
        $issuedAt = new \DateTimeImmutable('now');
        $lineItems = new ArrayCollection([$lineItem->getWrappedObject()]);

        $this->beConstructedWith(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '007',
            $issuedAt,
            $billingData,
            'USD',
            300,
            10300,
            $lineItems
        );

        $this->id()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $this->orderNumber()->shouldReturn('007');
        $this->billingData()->shouldReturn($billingData);
        $this->currencyCode()->shouldReturn('USD');
        $this->taxTotal()->shouldReturn(300);
        $this->total()->shouldReturn(10300);
        $this->lineItems()->shouldReturn($lineItems);
    }
}
