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

final class Version20190607001446 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_channel DROP FOREIGN KEY FK_16C8119E5CDB2AEB');
            $this->addSql('DROP INDEX UNIQ_16C8119E5CDB2AEB ON sylius_channel');
            $this->addSql('ALTER TABLE sylius_channel DROP billing_data_id, DROP taxId');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_channel DROP CONSTRAINT FK_16C8119E5CDB2AEB');
            $this->addSql('DROP INDEX UNIQ_16C8119E5CDB2AEB');
            $this->addSql('ALTER TABLE sylius_channel DROP billing_data_id, DROP taxId');
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_channel ADD billing_data_id INT DEFAULT NULL, ADD taxId VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
            $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E5CDB2AEB ON sylius_channel (billing_data_id)');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_channel ADD COLUMN billing_data_id INT DEFAULT NULL, ADD COLUMN taxId VARCHAR(255) DEFAULT NULL');
            $this->addSql('ALTER TABLE sylius_channel ADD CONSTRAINT FK_16C8119E5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_16C8119E5CDB2AEB ON sylius_channel (billing_data_id)');
        }
    }
}
