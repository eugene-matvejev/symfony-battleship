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

        $this->addSql('RENAME TABLE players TO users');
        $this->addSql('CREATE TABLE user_sessions (id INT AUTO_INCREMENT NOT NULL, player INT NOT NULL, hash VARCHAR(40) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_7AED791398197A65 (player), INDEX INDEX_SESSION_HASH (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_sessions ADD CONSTRAINT FK_7AED791398197A65 FOREIGN KEY (player) REFERENCES users (id)');
        $this->addSql('DROP INDEX INDEX_PLAYER_NAME ON users');
        $this->addSql('ALTER TABLE users CHANGE name email VARCHAR(255) NOT NULL, ADD passwordHash VARCHAR(40) NOT NULL');
        $this->addSql('CREATE INDEX INDEX_USER_EMAIL ON users (email)');
        $this->addSql('CREATE INDEX INDEX_USER_EMAIL_AND_PASSWORD ON users (email, passwordHash)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE users TO players');
        $this->addSql('DROP TABLE user_sessions');
        $this->addSql('DROP INDEX INDEX_USER_EMAIL ON users');
        $this->addSql('DROP INDEX INDEX_USER_EMAIL_AND_PASSWORD ON users');
        $this->addSql('ALTER TABLE users CHANGE email name VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci, DROP passwordHash');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON users (name)');
    }
}
