<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Invoice\Shop;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\InvoicingPlugin\Checker\InvoiceCustomerRelationCheckerInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceNotAccessible;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Invoice\Shop\ShopInvoiceDownloaderInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class ShopInvoiceDownloaderSpec extends ObjectBehavior
{
    function let(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceCustomerRelationCheckerInterface $invoiceCustomerRelationChecker
    ): void {
        $this->beConstructedWith($invoicePdfFileGenerator, $invoiceCustomerRelationChecker);
    }

    function it_implements_shop_invoice_downloader_interface(): void
    {
        $this->shouldImplement(ShopInvoiceDownloaderInterface::class);
    }

    function it_returns_pdf_file_with_an_invoice(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceCustomerRelationCheckerInterface $invoiceCustomerRelationChecker,
        CustomerInterface $customer
    ): void {
        $invoicePdf = new InvoicePdf('', '');

        $invoiceCustomerRelationChecker->check('0000001', $customer)->shouldBeCalled();

        $invoicePdfFileGenerator->generate('0000001')->willReturn($invoicePdf);

        $this->download('0000001', $customer)->shouldReturn($invoicePdf);
    }

    function it_propagates_exception_if_invoice_does_not_belong_to_given_customer(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceCustomerRelationCheckerInterface $invoiceCustomerRelationChecker,
        CustomerInterface $customer
    ): void {
        $customer->getId()->willReturn(0000002);

        $invoiceCustomerRelationChecker
            ->check('0000001', $customer)
            ->willThrow(new InvoiceNotAccessible('Invoice with id "0000001" is not accessible for user with id 2'));

        $invoicePdfFileGenerator->generate('0000001')->shouldNotHaveBeenCalled();

        $this
            ->shouldThrow(new InvoiceNotAccessible('Invoice with id "0000001" is not accessible for user with id 2'))
            ->during('download', ['0000001', $customer])
        ;
    }
}
