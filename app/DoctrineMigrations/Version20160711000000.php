<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160711000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('CREATE TABLE player_sessions (id INT AUTO_INCREMENT NOT NULL, player INT NOT NULL, hash VARCHAR(40) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_9CB7DCF398197A65 (player), INDEX INDEX_SESSION_HASH (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_sessions ADD CONSTRAINT FK_9CB7DCF398197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('DROP INDEX INDEX_PLAYER_NAME ON players');
        $this->addSql('ALTER TABLE players ADD email VARCHAR(25) NOT NULL, ADD passwordHash VARCHAR(40) NOT NULL, DROP name');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL ON players (email)');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, passwordHash)');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('DROP TABLE player_sessions');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL ON players');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players');
        $this->addSql('ALTER TABLE players ADD name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, DROP email, DROP passwordHash');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
    }
}
