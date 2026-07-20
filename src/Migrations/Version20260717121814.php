<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260717121814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique index for invoice number and sequence scope, and modify messenger messages indexes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SYLIUS_INVOICING_INVOICE_NUMBER ON sylius_invoicing_plugin_invoice (number)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence ADD year INT DEFAULT 0 NOT NULL, ADD month INT DEFAULT 0 NOT NULL, ADD type VARCHAR(255) DEFAULT \'global\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SYLIUS_INVOICING_SEQUENCE_SCOPE ON sylius_invoicing_plugin_sequence (type, year, month)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_SYLIUS_INVOICING_INVOICE_NUMBER ON sylius_invoicing_plugin_invoice');
        $this->addSql('DROP INDEX UNIQ_SYLIUS_INVOICING_SEQUENCE_SCOPE ON sylius_invoicing_plugin_sequence');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence DROP year, DROP month, DROP type');
    }
}
