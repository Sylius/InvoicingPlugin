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
use Sylius\InvoicingPlugin\Resolver\AnnuallySequenceScopeResolver;
use Sylius\InvoicingPlugin\Resolver\GlobalSequenceScopeResolver;
use Sylius\InvoicingPlugin\Resolver\MonthlySequenceScopeResolver;
use Sylius\InvoicingPlugin\Resolver\SequenceScopeResolverInterface;
use Symfony\Component\Clock\ClockInterface;

final class SequentialInvoiceNumberGenerator implements InvoiceNumberGenerator
{
    /** @var iterable<SequenceScopeResolverInterface> */
    private readonly iterable $scopeResolvers;

    /** @param iterable<SequenceScopeResolverInterface>|null $scopeResolvers */
    public function __construct(
        private readonly RepositoryInterface $sequenceRepository,
        private readonly FactoryInterface $sequenceFactory,
        private readonly EntityManagerInterface $sequenceManager,
        private readonly ClockInterface $clock,
        private readonly int $startNumber = 1,
        private readonly int $numberLength = 9,
        ?iterable $scopeResolvers = null,
        private readonly string $scope = InvoiceSequenceInterface::SCOPE_GLOBAL,
    ) {
        if (null === $scopeResolvers) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.3',
                'Not passing a value for the "$scopeResolvers" argument to "%s" is deprecated and the argument will be required in 3.0.',
                self::class,
            );

            $scopeResolvers = [
                new GlobalSequenceScopeResolver(),
                new MonthlySequenceScopeResolver(),
                new AnnuallySequenceScopeResolver(),
            ];
        }

        $this->scopeResolvers = $scopeResolvers;
    }

    public function generate(): string
    {
        $now = $this->clock->now();
        $scopeResolver = $this->getScopeResolver();

        $sequence = $this->getSequence($scopeResolver->resolve($now));

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();

        return $scopeResolver->prefix($now) . $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', \STR_PAD_LEFT);
    }

    /** @param array{year: int, month: int} $scopeCriteria */
    private function getSequence(array $scopeCriteria): InvoiceSequenceInterface
    {
        $criteria = array_merge(['type' => $this->scope], $scopeCriteria);

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

    private function getScopeResolver(): SequenceScopeResolverInterface
    {
        foreach ($this->scopeResolvers as $scopeResolver) {
            if ($scopeResolver->supports($this->scope)) {
                return $scopeResolver;
            }
        }

        throw SequenceScopeNotSupported::withScope($this->scope);
    }
}
