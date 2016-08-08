<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160724000001 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE player_sessions (id INT AUTO_INCREMENT NOT NULL, player INT NOT NULL, hash VARCHAR(40) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_9CB7DCF398197A65 (player), INDEX INDEX_SESSION_HASH (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_sessions ADD CONSTRAINT FK_9CB7DCF398197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('DROP INDEX INDEX_PLAYER_NAME ON players');
        $this->addSql('ALTER TABLE players ADD passwordHash VARCHAR(40) NOT NULL, CHANGE name email VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL ON players (email)');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, passwordHash)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE player_sessions');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL ON players');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players');
        $this->addSql('ALTER TABLE players DROP passwordHash, CHANGE email name VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
    }
}
