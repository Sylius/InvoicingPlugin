<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607115930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_3AA279BF551F0F81 ON sylius_invoicing_plugin_invoice');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF8D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('CREATE INDEX IDX_3AA279BF8D9F6D38 ON sylius_invoicing_plugin_invoice (order_id)');
        $this->addSql(
            'UPDATE sylius_invoicing_plugin_invoice
            SET sylius_invoicing_plugin_invoice.order_id = (
                SELECT sylius_order.id FROM sylius_order WHERE sylius_order.number = sylius_invoicing_plugin_invoice.order_number
            );'
        );
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP order_number');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF8D9F6D38');
        $this->addSql('DROP INDEX IDX_3AA279BF8D9F6D38 ON sylius_invoicing_plugin_invoice');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD order_number VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('CREATE INDEX IDX_3AA279BF551F0F81 ON sylius_invoicing_plugin_invoice (order_number)');
        $this->addSql(
            'UPDATE sylius_invoicing_plugin_invoice
            SET sylius_invoicing_plugin_invoice.order_number = (
                SELECT sylius_order.number FROM sylius_order WHERE sylius_order.id = sylius_invoicing_plugin_invoice.order_id
            );'
        );
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP order_id');
    }
}
