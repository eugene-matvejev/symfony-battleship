<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160521000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on "sqlite"');

        $this->addSql('CREATE TABLE battlefields (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_GAME ON battlefields (game)');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_PLAYER ON battlefields (player)');
        $this->addSql('CREATE TABLE cells (id INTEGER NOT NULL, battlefield INTEGER NOT NULL, coordinate VARCHAR(3) NOT NULL, flags INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_CELLS_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('CREATE UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD ON cells (battlefield, coordinate)');
        $this->addSql('CREATE TABLE games (id INTEGER NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game_results (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A619B3B232B318C ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_GAME ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_WINNER ON game_results (player)');
        $this->addSql('CREATE TABLE players (id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, flags INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on "sqlite"');

        $this->addSql('DROP TABLE battlefields');
        $this->addSql('DROP TABLE cells');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('DROP TABLE players');
    }
}
