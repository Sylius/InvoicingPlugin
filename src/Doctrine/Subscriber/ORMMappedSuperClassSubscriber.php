<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Doctrine\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Sylius\Bundle\ResourceBundle\EventListener\ORMMappedSuperClassSubscriber as BaseORMMappedSuperClassSubscriber;

final class ORMMappedSuperClassSubscriber implements EventSubscriber
{
    /** @var BaseORMMappedSuperClassSubscriber */
    private $baseOrmMappedSuperClassSubscriber;

    public function __construct(BaseORMMappedSuperClassSubscriber $baseOrmMappedSuperClassSubscriber)
    {
        $this->baseOrmMappedSuperClassSubscriber = $baseOrmMappedSuperClassSubscriber;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            'preEmbeddableResolve',
        ];
    }

    public function preEmbeddableResolve(LoadClassMetadataEventArgs $eventArgs): void
    {
        $this->baseOrmMappedSuperClassSubscriber->loadClassMetadata($eventArgs);
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $this->baseOrmMappedSuperClassSubscriber->loadClassMetadata($eventArgs);
    }
}
