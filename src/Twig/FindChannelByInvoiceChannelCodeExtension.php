<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Twig;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Twig\TwigFunction;

final class FindChannelByInvoiceChannelCodeExtension extends \Twig_Extension implements FindChannelByInvoiceChannelCodeExtensionInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('get_channel_by_invoice_channel_code', [$this, 'findByInvoiceChannelCode'])];
    }

    public function findByInvoiceChannelCode(string $invoiceChannelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($invoiceChannelCode);

        return $channel;
    }
}
