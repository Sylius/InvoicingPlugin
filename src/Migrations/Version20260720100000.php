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
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20260720100000 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Add unique index for invoice number and sequence scope on PostgreSQL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SYLIUS_INVOICING_INVOICE_NUMBER ON sylius_invoicing_plugin_invoice (number)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence ADD COLUMN year INT DEFAULT 0 NOT NULL, ADD COLUMN month INT DEFAULT 0 NOT NULL, ADD COLUMN type VARCHAR(255) DEFAULT \'global\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SYLIUS_INVOICING_SEQUENCE_SCOPE ON sylius_invoicing_plugin_sequence (type, year, month)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_SYLIUS_INVOICING_INVOICE_NUMBER');
        $this->addSql('DROP INDEX UNIQ_SYLIUS_INVOICING_SEQUENCE_SCOPE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence DROP COLUMN year, DROP COLUMN month, DROP COLUMN type');
    }
}
