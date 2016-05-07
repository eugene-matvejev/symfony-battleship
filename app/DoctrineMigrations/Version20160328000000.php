<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160328000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('DROP INDEX INDEX_BATTLEFIELD_UNIQUE_CELL ON cells');
        $this->addSql('ALTER TABLE cells ADD coordinate VARCHAR(3) NOT NULL, DROP x, DROP y');
        $this->addSql('CREATE UNIQUE INDEX INDEX_BATTLEFIELD_UNIQUE_CELL ON cells (battlefield, coordinate)');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('DROP INDEX INDEX_BATTLEFIELD_UNIQUE_CELL ON cells');
        $this->addSql('ALTER TABLE cells ADD x INT NOT NULL, ADD y INT NOT NULL, DROP coordinate');
        $this->addSql('CREATE UNIQUE INDEX INDEX_BATTLEFIELD_UNIQUE_CELL ON cells (battlefield, x, y)');
    }
}
