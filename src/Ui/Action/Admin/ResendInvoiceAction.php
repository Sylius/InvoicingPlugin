<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Admin;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ResendInvoiceAction
{
    private InvoiceRepositoryInterface $invoiceRepository;

    private OrderRepositoryInterface $orderRepository;

    private InvoiceEmailSenderInterface $invoiceEmailSender;

    private UrlGeneratorInterface $urlGenerator;

    private Session $session;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceEmailSenderInterface $invoiceEmailSender,
        OrderRepositoryInterface $orderRepository,
        UrlGeneratorInterface $urlGenerator,
        Session $session
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceEmailSender = $invoiceEmailSender;
        $this->orderRepository = $orderRepository;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    public function __invoke(string $id): Response
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->find($id);
        Assert::notNull($invoice);

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['number' => $invoice->orderNumber()]);
        Assert::notNull($order);

        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        try {
            $this->invoiceEmailSender->sendInvoiceEmail($invoice, $customer->getEmail());
        } catch (\Exception $exception) {
            $this->session->getFlashBag()->add(
                'failure',
                $exception->getMessage()
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_admin_order_show', ['id' => $order->getId()])
            );
        }

        $this->session->getFlashBag()->add(
            'success',
            'sylius_invoicing_plugin.invoice_resent_successfully'
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_admin_order_show', ['id' => $order->getId()])
        );
    }
}
