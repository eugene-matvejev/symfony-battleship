<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151019003758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gamesResults (id INT AUTO_INCREMENT NOT NULL, game INT DEFAULT NULL, player INT DEFAULT NULL, timestamp DATETIME NOT NULL, UNIQUE INDEX UNIQ_6B05D5BE232B318C (game), INDEX IDX_6B05D5BE98197A65 (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BE232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BE98197A65 FOREIGN KEY (player) REFERENCES players (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE gamesResults');
    }
}
