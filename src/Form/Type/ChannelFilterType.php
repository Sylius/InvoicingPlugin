<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Form\Type;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelFilterType extends AbstractType
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('channel', ChoiceType::class, [
            'choices' => $this->getChannelsList(),
            'label' => false,
            'placeholder' => 'sylius.ui.all',
        ]);
    }

    private function getChannelsList(): array
    {
        $channels = [];

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findBy(['enabled' => true]) as $channel) {
            $channels[$channel->getName()] = $channel->getCode();
        }

        return $channels;
    }
}
