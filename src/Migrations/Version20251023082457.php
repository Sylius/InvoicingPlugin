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

final class Version20251023082457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add path field to sylius_invoicing_plugin_invoice table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD path VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AA279BFB548B0F ON sylius_invoicing_plugin_invoice (path)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_3AA279BFB548B0F ON sylius_invoicing_plugin_invoice');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP path');
    }
}
