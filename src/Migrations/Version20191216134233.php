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

final class Version20191216134233 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFCFE4AA36');
        $this->addSql('DROP INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AA279BFB5282EDF ON sylius_invoicing_plugin_invoice (shop_billing_data_id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFCFE4AA36 FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFCFE4AA36');
        $this->addSql('DROP INDEX UNIQ_3AA279BFB5282EDF ON sylius_invoicing_plugin_invoice');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice (shop_billing_data_id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFCFE4AA36 FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
    }
}
