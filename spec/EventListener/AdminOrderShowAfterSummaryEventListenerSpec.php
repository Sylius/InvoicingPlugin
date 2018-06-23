<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\EventListener\AdminOrderShowAfterSummaryEventListener;

final class AdminOrderShowAfterSummaryEventListenerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('@SyliusInvoicingPlugin/Order/_invoices.html.twig');
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(AdminOrderShowAfterSummaryEventListener::class);
    }

    public function it_adds_block_to_event(BlockEvent $event, OrderInterface $order): void
    {
        $event->getSettings()->willReturn(['template' => 'testTemplate']);
        $event->getSetting('resource')->willReturn($order);

        $event->addBlock(Argument::type(Block::class))->shouldBeCalled();

        $this->onBlockEvent($event);
    }
}
