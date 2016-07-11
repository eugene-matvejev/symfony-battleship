<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160711105918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players');
        $this->addSql('ALTER TABLE players CHANGE password passwordHash VARCHAR(40) NOT NULL');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, passwordHash)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players');
        $this->addSql('ALTER TABLE players CHANGE passwordhash password VARCHAR(40) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, password)');
    }
}
