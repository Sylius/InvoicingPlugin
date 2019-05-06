<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Doctrine\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain as BaseMappingDriverChain;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

final class DriverChain implements MappingDriver
{
    /** @var BaseMappingDriverChain */
    private $baseMappingDriverChain;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(BaseMappingDriverChain $baseMappingDriverChain, EntityManager $entityManager)
    {
        $this->baseMappingDriverChain = $baseMappingDriverChain;
        $this->entityManager = $entityManager;
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        $this->baseMappingDriverChain->loadMetadataForClass($className, $metadata);
        if (
            $this->entityManager->getEventManager()->hasListeners('preEmbeddableResolve') &&
            !empty($metadata->embeddedClasses)
        ) {
            $eventArgs = new LoadClassMetadataEventArgs($metadata, $this->entityManager);
            $this->entityManager->getEventManager()->dispatchEvent('preEmbeddableResolve', $eventArgs);
        }
    }

    public function getAllClassNames(): array
    {
        return $this->baseMappingDriverChain->getAllClassNames();
    }

    public function isTransient($className): bool
    {
        return $this->baseMappingDriverChain->isTransient($className);
    }

    public function getDrivers(): array
    {
        return $this->baseMappingDriverChain->getDrivers();
    }

    public function getDefaultDriver(): ?MappingDriver
    {
        return $this->baseMappingDriverChain->getDefaultDriver();
    }
}
