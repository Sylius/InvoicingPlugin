<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\SequentialInvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SequentialInvoiceIdentifierGeneratorSpec extends ObjectBehavior
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

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SequentialInvoiceIdentifierGenerator::class);
    }

    function it_implements_invoice_identifier_generator_interface(): void
    {
        $this->shouldImplement(InvoiceIdentifierGenerator::class);
    }

    function it_generates_invoice_identifier(
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

        $this->generate()->shouldReturn($dateTime->format('y-m-d' . '-000000001'));
    }

    function it_generates_invoice_identifier_when_sequence_is_null(
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

        $this->generate()->shouldReturn($dateTime->format('y-m-d' . '-000000001'));
    }
}
