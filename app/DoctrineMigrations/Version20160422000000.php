<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160422000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE cells CHANGE mask flag INT NOT NULL');
        $this->addSql('ALTER TABLE players CHANGE mask flag INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE cells CHANGE flag mask INT NOT NULL');
        $this->addSql('ALTER TABLE players CHANGE flag mask INT NOT NULL');
    }
}
