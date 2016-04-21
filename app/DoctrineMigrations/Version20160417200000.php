<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160417200000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A68CDE5729');
        $this->addSql('DROP TABLE player_types');
        $this->addSql('DROP INDEX IDX_264E43A68CDE5729 ON players');
        $this->addSql('ALTER TABLE players CHANGE type mask INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('CREATE TABLE player_types (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE players CHANGE mask type INT NOT NULL');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A68CDE5729 FOREIGN KEY (type) REFERENCES player_types (id)');
        $this->addSql('CREATE INDEX IDX_264E43A68CDE5729 ON players (type)');
    }
}
