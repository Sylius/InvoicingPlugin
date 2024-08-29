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
final class Version20220811054008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'original_unit_net_price field added';
    }

    public function up(Schema $schema): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD discounted_unit_net_price INT DEFAULT NULL');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD COLUMN discounted_unit_net_price INT DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP discounted_unit_net_price');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP COLUMN discounted_unit_net_price');
        }
    }
}
