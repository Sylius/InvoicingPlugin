<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Twig;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Twig\Extension\AbstractExtension;

final class FindChannelByInvoiceChannelCodeExtension extends AbstractExtension
    implements FindChannelByInvoiceChannelCodeExtensionInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function getFilters(): array
    {
        return array(new TwigFilter('invoice_channel_code_to_channel', array($this, 'findByInvoiceChannelCode')));
    }

    public function findByInvoiceChannelCode(string $invoiceChannelCode): ChannelInterface
    {
        return $this->channelRepository->findOneByCode($invoiceChannelCode);
    }
}
