<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151103234126 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games ADD result INT DEFAULT NULL');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31136AC113 FOREIGN KEY (result) REFERENCES gamesResults (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF232B31136AC113 ON games (result)');
        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BE232B318C');
        $this->addSql('DROP INDEX UNIQ_6B05D5BE232B318C ON gamesResults');
        $this->addSql('ALTER TABLE gamesResults DROP game');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31136AC113');
        $this->addSql('DROP INDEX UNIQ_FF232B31136AC113 ON games');
        $this->addSql('ALTER TABLE games DROP result');
        $this->addSql('ALTER TABLE gamesResults ADD game INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BE232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B05D5BE232B318C ON gamesResults (game)');
    }
}
