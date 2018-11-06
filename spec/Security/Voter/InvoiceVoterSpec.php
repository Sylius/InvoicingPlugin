<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Security\Voter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class InvoiceVoterSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_a_symfony_security_voter(): void
    {
        $this->shouldHaveType(VoterInterface::class);
    }

    function it_abstains_from_making_a_decision_if_attribute_is_not_supported(TokenInterface $token, InvoiceInterface $invoice): void
    {
        $this->vote($token, $invoice, ['random'])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_abstains_from_making_a_decision_if_subject_is_not_supported(TokenInterface $token): void
    {
        $this->vote($token, new \stdClass(), [InvoiceVoter::ACCESS])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_does_not_allow_accessing_an_invoice_if_user_is_not_logged_in(TokenInterface $token, InvoiceInterface $invoice): void
    {
        $this->vote($token, $invoice, [InvoiceVoter::ACCESS])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_allows_accesings_an_invoice_if_user_is_logged_as_admin(
        TokenInterface $token,
        InvoiceInterface $invoice,
        AdminUserInterface $adminUser
    ): void {
        $token->getUser()->willReturn($adminUser);

        $this->vote($token, $invoice, [InvoiceVoter::ACCESS])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_does_not_allow_accessing_an_invoice_if_user_has_not_placed_the_order_related_to_the_invoice(
        OrderRepositoryInterface $orderRepository,
        TokenInterface $token,
        InvoiceInterface $invoice,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $invoice->orderNumber()->willReturn('1337');

        $token->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $orderRepository->findOneByNumberAndCustomer('1337', $customer)->willReturn(null);

        $this->vote($token, $invoice, [InvoiceVoter::ACCESS])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_allows_accessing_an_invoice_if_user_has_placed_the_order_related_to_the_invoice(
        OrderRepositoryInterface $orderRepository,
        TokenInterface $token,
        InvoiceInterface $invoice,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        OrderInterface $order
    ): void {
        $invoice->orderNumber()->willReturn('1337');

        $token->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $orderRepository->findOneByNumberAndCustomer('1337', $customer)->willReturn($order);

        $this->vote($token, $invoice, [InvoiceVoter::ACCESS])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }
}
