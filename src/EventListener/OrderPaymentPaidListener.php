<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\EmailManager\InvoiceEmailManagerInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Component\Templating\EngineInterface;

final class OrderPaymentPaidListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var InvoiceEmailManagerInterface */
    private $emailSender;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        GeneratorInterface $pdfGenerator,
        InvoiceEmailManagerInterface $emailSender,
        EngineInterface $templatingEngine
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->pdfGenerator= $pdfGenerator;
        $this->emailSender = $emailSender;
        $this->orderRepository = $orderRepository;
        $this->templatingEngine = $templatingEngine;
    }

    public function __invoke(OrderPaymentPaid $event)
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findOneByOrderNumber($event->orderNumber());

        $invoiceFile = new PdfResponse(
            $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render('@SyliusInvoicingPlugin/Resources/views/Invoice/Email/invoiceGenerated.html.twig', [
                'invoice' => $invoice
            ])),
            $invoice->id() . '.pdf'
        );

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $event->orderNumber()]);

        $this->emailSender->sendInvoiceEmail($invoice, $invoiceFile, $order->getCustomer()->getEmail());
    }
}
