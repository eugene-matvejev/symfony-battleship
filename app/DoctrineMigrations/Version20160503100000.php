<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160503100000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE players CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('DROP INDEX INDEX_PLAYER_NAME ON players');
        $this->addSql('ALTER TABLE players CHANGE name name VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci');
    }
}
