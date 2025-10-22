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

final class Version20251021074051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add year, month and type columns to sylius_invoicing_plugin_sequence table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence ADD year INT DEFAULT NULL, ADD month INT DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence DROP year, DROP month, DROP type');
    }
}
