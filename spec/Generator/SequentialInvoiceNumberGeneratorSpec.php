<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;

final class SequentialInvoiceNumberGeneratorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider
    ): void {
        $this->beConstructedWith(
            $sequenceRepository,
            $sequenceFactory,
            $sequenceManager,
            $dateTimeProvider,
            1,
            9
        );
    }

    function it_implements_invoice_number_generator_interface(): void
    {
        $this->shouldImplement(InvoiceNumberGenerator::class);
    }

    function it_generates_invoice_number(
        RepositoryInterface $sequenceRepository,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider,
        InvoiceSequenceInterface $sequence
    ): void {
        $dateTime = new \DateTime('now');

        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate()->shouldReturn($dateTime->format('Y/m') . '/000000001');
    }

    function it_generates_invoice_number_when_sequence_is_null(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider,
        InvoiceSequenceInterface $sequence
    ): void {
        $dateTime = new \DateTime('now');

        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);

        $sequenceManager->persist($sequence)->shouldBeCalled();

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate()->shouldReturn($dateTime->format('Y/m') . '/000000001');
    }
}
