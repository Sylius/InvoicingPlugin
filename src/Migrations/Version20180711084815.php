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
final class Version20180711084815 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_sequence (id INTEGER NOT NULL, idx INTEGER NOT NULL, version INTEGER DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD number VARCHAR(255) NOT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence CHANGE id id INT AUTO_INCREMENT NOT NULL');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('CREATE SEQUENCE sylius_invoicing_plugin_sequence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_sequence ( id INTEGER NOT NULL, idx INTEGER NOT NULL, version INTEGER DEFAULT 1 NOT NULL, PRIMARY KEY(id) )');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD number VARCHAR(255) NOT NULL');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence ALTER id SET DEFAULT nextval(\'sylius_invoicing_plugin_sequence_id_seq\')');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP number');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence CHANGE id id INT NOT NULL');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_sequence');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP number');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence ALTER id DROP DEFAULT');
            $this->addSql('DROP SEQUENCE sylius_invoicing_plugin_sequence_id_seq');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_sequence');
        }
    }
}
