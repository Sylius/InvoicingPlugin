<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;

final class InvoiceContext implements Context
{
    /** @var RepositoryInterface */
    private $sequenceRepository;

    /** @var FactoryInterface */
    private $sequenceFactory;

    /** @var EntityManagerInterface */
    private $sequenceManager;

    public function __construct(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * @Given /^the last invoice has been generated today as (\d)(?:st|nd|rd|th) in this month$/
     */
    public function theLastInvoiceHasBeenGeneratedToday(int $number): void
    {
        $sequence = $this->getSequence();
        $sequence->setLastGeneratedAt(new \DateTimeImmutable('now'));
        for ($i = 0; $i < $number; ++$i) {
            $sequence->incrementIndex();
        }

        $this->sequenceManager->flush();
    }

    /**
     * @Given /^the last invoice has been generated two months ago as (\d)(?:st|nd|rd|th) in that month$/
     */
    public function theLastInvoiceHasBeenGeneratedOneMonthAgo(int $number): void
    {
        $sequence = $this->getSequence();
        $sequence->setLastGeneratedAt(new \DateTimeImmutable('-2 months'));
        for ($i = 0; $i < $number; ++$i) {
            $sequence->incrementIndex();
        }

        $this->sequenceManager->flush();
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
}
