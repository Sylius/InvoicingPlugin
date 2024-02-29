<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ResendInvoiceAction
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceEmailSenderInterface $invoiceEmailSender,
        private OrderRepositoryInterface $orderRepository,
        private UrlGeneratorInterface $urlGenerator,
        private SessionInterface | RequestStack $requestStackOrSession,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation('sylius/invoicing-plugin', '0.24', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius Invoicing Plugin 0.24 and will be removed in 1.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
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
            $this->getFlashBag()->add('failure', $exception->getMessage());

            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_admin_order_show', ['id' => $order->getId()])
            );
        }

        $this->getFlashBag()->add('success', 'sylius_invoicing_plugin.invoice_resent_successfully');

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_admin_order_show', ['id' => $order->getId()])
        );
    }

    private function getFlashBag(): FlashBagInterface
    {
        if ($this->requestStackOrSession instanceof RequestStack) {
            return $this->requestStackOrSession->getSession()->getBag('flashes');
        }

        return $this->requestStackOrSession->getBag('flashes');
    }
}
