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

namespace Tests\Sylius\InvoicingPlugin\Unit\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class InvoiceVoterTest extends TestCase
{
    #[Test]
    public function it_is_a_symfony_security_voter(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $voter = new InvoiceVoter($orderRepository);

        self::assertInstanceOf(VoterInterface::class, $voter);
    }

    #[Test]
    public function it_abstains_from_making_a_decision_if_attribute_is_not_supported(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, $invoice, ['random']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function it_abstains_from_making_a_decision_if_subject_is_not_supported(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, new \stdClass(), [InvoiceVoter::ACCESS]);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function it_does_not_allow_accessing_an_invoice_if_user_is_not_logged_in(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $token->method('getUser')->willReturn(null);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, $invoice, [InvoiceVoter::ACCESS]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    #[Test]
    public function it_allows_accessing_an_invoice_if_user_is_logged_as_admin(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $adminUser = $this->createMock(AdminUserInterface::class);

        $token->method('getUser')->willReturn($adminUser);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, $invoice, [InvoiceVoter::ACCESS]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function it_does_not_allow_accessing_an_invoice_if_user_has_not_placed_the_order_related_to_the_invoice(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $shopUser = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $invoice->method('order')->willReturn($order);
        $order->method('getNumber')->willReturn('1337');
        $token->method('getUser')->willReturn($shopUser);
        $shopUser->method('getCustomer')->willReturn($customer);
        $orderRepository->method('findOneByNumberAndCustomer')->with('1337', $customer)->willReturn(null);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, $invoice, [InvoiceVoter::ACCESS]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    #[Test]
    public function it_allows_accessing_an_invoice_if_user_has_placed_the_order_related_to_the_invoice(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $shopUser = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $invoice->method('order')->willReturn($order);
        $order->method('getNumber')->willReturn('1337');
        $token->method('getUser')->willReturn($shopUser);
        $shopUser->method('getCustomer')->willReturn($customer);
        $orderRepository->method('findOneByNumberAndCustomer')->with('1337', $customer)->willReturn($order);

        $voter = new InvoiceVoter($orderRepository);
        $result = $voter->vote($token, $invoice, [InvoiceVoter::ACCESS]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
