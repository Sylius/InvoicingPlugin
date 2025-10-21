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
use Sylius\InvoicingPlugin\Enum\InvoiceSequenceScopeEnum;
use Symfony\Component\Clock\ClockInterface;

final class SequentialInvoiceNumberGenerator implements InvoiceNumberGenerator
{
    public function __construct(
        private readonly RepositoryInterface $sequenceRepository,
        private readonly FactoryInterface $sequenceFactory,
        private readonly EntityManagerInterface $sequenceManager,
        private readonly ClockInterface $clock,
        private readonly int $startNumber = 1,
        private readonly int $numberLength = 9,
        private readonly ?string $scope = null,
    ) {
        if (null === $this->scope) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.1',
                'Not passing a "%s" to "%s" is deprecated and will be required in SyliusInvoicingPlugin 3.0.',
                InvoiceSequenceScopeEnum::class,
                self::class,
            );
        }
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
        $now = $this->clock->now();
        $scope = InvoiceSequenceScopeEnum::tryFrom($this->scope ?? '') ?? InvoiceSequenceScopeEnum::GLOBAL;

        $criteria = match ($scope) {
            InvoiceSequenceScopeEnum::MONTHLY => [
                'year' => (int) $now->format('Y'),
                'month' => (int) $now->format('m'),
            ],
            InvoiceSequenceScopeEnum::ANNUALLY => [
                'year' => (int) $now->format('Y'),
            ],
            InvoiceSequenceScopeEnum::GLOBAL => [],
        };

        /** @var InvoiceSequenceInterface|null $sequence */
        $sequence = $this->sequenceRepository->findOneBy($criteria);

        if (null !== $sequence) {
            return $sequence;
        }

        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();

        if (isset($criteria['year'])) {
            $sequence->setYear($criteria['year']);
        }

        if (isset($criteria['month'])) {
            $sequence->setMonth($criteria['month']);
        }

        $this->sequenceManager->persist($sequence);

        return $sequence;
    }
}
