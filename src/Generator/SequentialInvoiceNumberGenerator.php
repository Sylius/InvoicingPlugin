<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;

final class SequentialInvoiceNumberGenerator implements InvoiceNumberGenerator
{
    /** @var RepositoryInterface */
    private $sequenceRepository;

    /** @var FactoryInterface */
    private $sequenceFactory;

    /** @var EntityManagerInterface */
    private $sequenceManager;

    /** @var int */
    private $startNumber;

    /** @var int */
    private $numberLength;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        DateTimeProvider $dateTimeProvider,
        int $startNumber = 1,
        int $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceManager = $sequenceManager;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    public function generate(): string
    {
        $currentDate = $this->dateTimeProvider->__invoke();

        $invoiceIdentifierPrefix = $currentDate->format('Y/m') . '/';

        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        if ($this->isMonthChanged($currentDate, $sequence->getLastGeneratedAt())) {
            $sequence->resetIndex();
        }

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();
        $sequence->setLastGeneratedAt($currentDate);

        return $invoiceIdentifierPrefix . $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', STR_PAD_LEFT);
    }

    private function getSequence(): InvoiceSequenceInterface
    {
        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null != $sequence) {
            return $sequence;
        }

        /** @var InvoiceSequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();
        $this->sequenceManager->persist($sequence);

        return $sequence;
    }

    private function isMonthChanged(\DateTimeInterface $currentDate, ?\DateTimeInterface $lastGenerationDate): bool
    {
        if ($lastGenerationDate === null) {
            return false;
        }

        return $lastGenerationDate->format('Y/m') !== $currentDate->format('Y/m');
    }
}
