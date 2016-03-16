<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160312210000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE cell_states DROP name');
        $this->addSql('ALTER TABLE player_types DROP name');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE cell_states ADD name VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE player_types ADD name VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci');
    }
}
