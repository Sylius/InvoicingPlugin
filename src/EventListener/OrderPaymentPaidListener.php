<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Knp\Snappy\GeneratorInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailManagerInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class OrderPaymentPaidListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var InvoiceEmailManagerInterface */
    private $emailSender;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        GeneratorInterface $pdfGenerator,
        InvoiceEmailManagerInterface $emailSender
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->pdfGenerator= $pdfGenerator;
        $this->emailSender = $emailSender;
    }

    public function __invoke(OrderPaymentPaid $event)
    {
        $invoice = $this->invoiceRepository->findOneByOrderNumber($event->orderNumber());

        $invoiceFile = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render('@SyliusInvoicingPlugin/Resources/views/Invoice/Download/pdf.html.twig', [
                'invoice' => $invoice
            ])
        );
    }
}
