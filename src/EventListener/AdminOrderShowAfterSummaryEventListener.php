<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

final class AdminOrderShowAfterSummaryEventListener
{
    /**
     * @var string
     */
    private $template;

    /**
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public function onBlockEvent(BlockEvent $event): void
    {
        $block = new Block();
        $block->setId(uniqid('invoices', true));
        $block->setSettings(array_replace($event->getSettings(), [
            'template' => $this->template,
            'resource' => $event->getSetting('resource')
        ]));
        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
