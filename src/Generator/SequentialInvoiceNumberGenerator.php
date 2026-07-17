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

namespace Sylius\InvoicingPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Sylius\InvoicingPlugin\Exception\SequenceScopeNotSupported;
use Sylius\InvoicingPlugin\Resolver\SequenceScopeResolverInterface;
use Symfony\Component\Clock\ClockInterface;

final class SequentialInvoiceNumberGenerator implements InvoiceNumberGenerator
{
    /** @param iterable<SequenceScopeResolverInterface> $scopeResolvers */
    public function __construct(
        private readonly RepositoryInterface $sequenceRepository,
        private readonly FactoryInterface $sequenceFactory,
        private readonly EntityManagerInterface $sequenceManager,
        private readonly ClockInterface $clock,
        private readonly iterable $scopeResolvers,
        private readonly int $startNumber = 1,
        private readonly int $numberLength = 9,
        private readonly string $scope = InvoiceSequenceInterface::SCOPE_GLOBAL,
    ) {
    }

    public function generate(): string
    {
        $invoiceIdentifierPrefix = $this->clock->now()->format('Y/m') . '/';

        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();

        return $invoiceIdentifierPrefix . $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', \STR_PAD_LEFT);
    }

    private function getSequence(): InvoiceSequenceInterface
    {
        $criteria = array_merge(
            ['type' => $this->scope],
            $this->resolveScopeCriteria($this->clock->now()),
        );

        /** @var InvoiceSequenceInterface|null $sequence */
        $sequence = $this->sequenceRepository->findOneBy($criteria);

        if (null !== $sequence) {
            return $sequence;
        }

        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();
        $sequence->setType($this->scope);
        $sequence->setYear($criteria['year']);
        $sequence->setMonth($criteria['month']);

        $this->sequenceManager->persist($sequence);

        return $sequence;
    }

    /** @return array{year: int, month: int} */
    private function resolveScopeCriteria(\DateTimeImmutable $now): array
    {
        foreach ($this->scopeResolvers as $scopeResolver) {
            if ($scopeResolver->supports($this->scope)) {
                return $scopeResolver->resolve($now);
            }
        }

        throw SequenceScopeNotSupported::withScope($this->scope);
    }
}
