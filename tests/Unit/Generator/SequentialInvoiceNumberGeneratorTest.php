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

namespace Tests\Sylius\InvoicingPlugin\Unit\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Sylius\InvoicingPlugin\Exception\SequenceScopeNotSupported;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;
use Sylius\InvoicingPlugin\Generator\SequentialInvoiceNumberGenerator;
use Sylius\InvoicingPlugin\Resolver\AnnuallySequenceScopeResolver;
use Sylius\InvoicingPlugin\Resolver\GlobalSequenceScopeResolver;
use Sylius\InvoicingPlugin\Resolver\MonthlySequenceScopeResolver;
use Symfony\Component\Clock\ClockInterface;

final class SequentialInvoiceNumberGeneratorTest extends TestCase
{
    private MockObject&RepositoryInterface $sequenceRepository;

    private FactoryInterface&MockObject $sequenceFactory;

    private EntityManagerInterface&MockObject $sequenceManager;

    private ClockInterface&MockObject $clock;

    private SequentialInvoiceNumberGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sequenceRepository = $this->createMock(RepositoryInterface::class);
        $this->sequenceFactory = $this->createMock(FactoryInterface::class);
        $this->sequenceManager = $this->createMock(EntityManagerInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);

        $this->generator = $this->createGenerator(InvoiceSequenceInterface::SCOPE_GLOBAL);
    }

    #[Test]
    public function it_implements_invoice_number_generator_interface(): void
    {
        self::assertInstanceOf(InvoiceNumberGenerator::class, $this->generator);
    }

    #[Test]
    public function it_generates_invoice_number(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('now');
        $this->clock->method('now')->willReturn($dateTime);

        $this->sequenceRepository
            ->method('findOneBy')
            ->with(['type' => InvoiceSequenceInterface::SCOPE_GLOBAL, 'year' => 0, 'month' => 0])
            ->willReturn($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence
            ->expects(self::once())
            ->method('incrementIndex');

        $result = $this->generator->generate();

        self::assertSame($dateTime->format('Y/m') . '/000000001', $result);
    }

    #[Test]
    public function it_generates_invoice_number_when_sequence_is_null(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('now');
        $this->clock->method('now')->willReturn($dateTime);

        $this->sequenceRepository
            ->method('findOneBy')
            ->with(['type' => InvoiceSequenceInterface::SCOPE_GLOBAL, 'year' => 0, 'month' => 0])
            ->willReturn(null);

        $this->sequenceFactory->method('createNew')->willReturn($sequence);
        $sequence->expects(self::once())->method('setType')->with(InvoiceSequenceInterface::SCOPE_GLOBAL);
        $sequence->expects(self::once())->method('setYear')->with(0);
        $sequence->expects(self::once())->method('setMonth')->with(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('persist')
            ->with($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence
            ->expects(self::once())
            ->method('incrementIndex');

        $result = $this->generator->generate();

        self::assertSame($dateTime->format('Y/m') . '/000000001', $result);
    }

    #[Test]
    public function it_generates_invoice_number_with_monthly_scope(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('2025-10-15');
        $this->clock->method('now')->willReturn($dateTime);

        $generator = $this->createGenerator(InvoiceSequenceInterface::SCOPE_MONTHLY);

        $this->sequenceRepository
            ->method('findOneBy')
            ->with(['type' => InvoiceSequenceInterface::SCOPE_MONTHLY, 'year' => 2025, 'month' => 10])
            ->willReturn($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence
            ->expects(self::once())
            ->method('incrementIndex');

        $result = $generator->generate();

        self::assertSame('2025/10/000000001', $result);
    }

    #[Test]
    public function it_generates_invoice_number_with_annually_scope(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('2025-11-15');
        $this->clock->method('now')->willReturn($dateTime);

        $generator = $this->createGenerator(InvoiceSequenceInterface::SCOPE_ANNUALLY);

        $this->sequenceRepository
            ->method('findOneBy')
            ->with(['type' => InvoiceSequenceInterface::SCOPE_ANNUALLY, 'year' => 2025, 'month' => 0])
            ->willReturn($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence
            ->expects(self::once())
            ->method('incrementIndex');

        $result = $generator->generate();

        self::assertSame('2025/11/000000001', $result);
    }

    #[Test]
    public function it_generates_invoice_number_when_monthly_sequence_is_null(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('2025-10-15');
        $this->clock->method('now')->willReturn($dateTime);

        $generator = $this->createGenerator(InvoiceSequenceInterface::SCOPE_MONTHLY);

        $scope = InvoiceSequenceInterface::SCOPE_MONTHLY;

        $this->sequenceRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['type' => $scope, 'year' => 2025, 'month' => 10])
            ->willReturn(null);

        $this->sequenceFactory->expects(self::once())->method('createNew')->willReturn($sequence);
        $sequence->expects(self::once())->method('setYear')->with(2025);
        $sequence->expects(self::once())->method('setMonth')->with(10);
        $sequence->expects(self::once())->method('setType')->with($scope);

        $this->sequenceManager
            ->expects(self::once())
            ->method('persist')
            ->with($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        $this->sequenceManager
            ->expects(self::once())
            ->method('lock')
            ->with($sequence, LockMode::OPTIMISTIC, 1);

        $sequence
            ->expects(self::once())
            ->method('incrementIndex');

        $result = $generator->generate();

        self::assertSame('2025/10/000000001', $result);
    }

    #[Test]
    public function it_throws_an_exception_when_no_resolver_supports_the_scope(): void
    {
        $this->clock->method('now')->willReturn(new \DateTimeImmutable('now'));

        $generator = $this->createGenerator('weekly');

        $this->expectException(SequenceScopeNotSupported::class);

        $generator->generate();
    }

    #[Test]
    public function it_falls_back_to_built_in_scope_resolvers_when_none_are_passed(): void
    {
        $sequence = $this->createMock(InvoiceSequenceInterface::class);

        $dateTime = new \DateTimeImmutable('2025-10-15');
        $this->clock->method('now')->willReturn($dateTime);

        $generator = new SequentialInvoiceNumberGenerator(
            $this->sequenceRepository,
            $this->sequenceFactory,
            $this->sequenceManager,
            $this->clock,
            1,
            9,
        );

        $this->sequenceRepository
            ->method('findOneBy')
            ->with(['type' => InvoiceSequenceInterface::SCOPE_GLOBAL, 'year' => 0, 'month' => 0])
            ->willReturn($sequence);

        $sequence->method('getVersion')->willReturn(1);
        $sequence->method('getIndex')->willReturn(0);

        self::assertSame('2025/10/000000001', $generator->generate());
    }

    private function createGenerator(string $scope): SequentialInvoiceNumberGenerator
    {
        return new SequentialInvoiceNumberGenerator(
            $this->sequenceRepository,
            $this->sequenceFactory,
            $this->sequenceManager,
            $this->clock,
            1,
            9,
            [
                new GlobalSequenceScopeResolver(),
                new MonthlySequenceScopeResolver(),
                new AnnuallySequenceScopeResolver(),
            ],
            $scope,
        );
    }
}
