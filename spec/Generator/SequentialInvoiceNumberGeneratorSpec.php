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
use Sylius\InvoicingPlugin\Generator\SequentialInvoiceNumberGenerator;

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

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SequentialInvoiceNumberGenerator::class);
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
        $lastGenerationDate = new \DateTime('now');
        $currentDate = new \DateTime('now');

        $dateTimeProvider->__invoke()->willReturn($currentDate);

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);
        $sequence->getLastGeneratedAt()->willReturn($lastGenerationDate);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();
        $sequence->setLastGeneratedAt($currentDate)->shouldBeCalled();

        $this->generate()->shouldReturn($currentDate->format('Y/m') . '/000000001');
    }

    function it_resets_index_if_month_and_year_have_changed(
        RepositoryInterface $sequenceRepository,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider,
        InvoiceSequenceInterface $sequence
    ): void {
        $lastGenerationDate = new \DateTime('-2 months');
        $currentDate = new \DateTime('now');

        $dateTimeProvider->__invoke()->willReturn($currentDate);

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->getVersion()->willReturn(1);
        $sequence->getLastGeneratedAt()->willReturn($lastGenerationDate);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->resetIndex()->shouldBeCalled();

        $sequence->getIndex()->willReturn(0);

        $sequence->incrementIndex()->shouldBeCalled();
        $sequence->setLastGeneratedAt($currentDate)->shouldBeCalled();

        $this->generate()->shouldReturn($currentDate->format('Y/m') . '/000000001');
    }

    function it_generates_invoice_number_when_sequence_is_null(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider,
        InvoiceSequenceInterface $sequence
    ): void {
        $currentDate = new \DateTime('now');

        $dateTimeProvider->__invoke()->willReturn($currentDate);

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);

        $sequenceManager->persist($sequence)->shouldBeCalled();

        $sequence->getVersion()->willReturn(1);
        $sequence->getIndex()->willReturn(0);
        $sequence->getLastGeneratedAt()->willReturn(null);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();
        $sequence->setLastGeneratedAt($currentDate)->shouldBeCalled();

        $this->generate()->shouldReturn($currentDate->format('Y/m') . '/000000001');
    }
}
