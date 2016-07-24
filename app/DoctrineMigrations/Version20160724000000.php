<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160724000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE player_sessions (id SERIAL NOT NULL, player INT NOT NULL, hash VARCHAR(40) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CB7DCF398197A65 ON player_sessions (player)');
        $this->addSql('CREATE INDEX INDEX_SESSION_HASH ON player_sessions (hash)');
        $this->addSql('ALTER TABLE player_sessions ADD CONSTRAINT FK_9CB7DCF398197A65 FOREIGN KEY (player) REFERENCES players (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX index_player_name');
        $this->addSql('ALTER TABLE players ADD passwordHash VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE players RENAME COLUMN name TO email');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL ON players (email)');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, passwordHash)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE player_sessions');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD');
        $this->addSql('ALTER TABLE players DROP passwordHash');
        $this->addSql('ALTER TABLE players RENAME COLUMN email TO name');
        $this->addSql('CREATE INDEX index_player_name ON players (name)');
    }
}
