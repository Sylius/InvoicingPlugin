<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPage;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getIssuedAtDate(): \DateTimeInterface
    {
        // Jun 19, 2018, 2:17:29 PM

        return \DateTimeImmutable::createFromFormat('M j, Y, g:i:s A', $this->getElement('issued_at')->getText());
    }

    public function getRouteName(): string
    {
        return 'sylius_invoicing_plugin_admin_invoice_show';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'issued_at' => '#invoice-issued-at',
        ]);
    }
}
