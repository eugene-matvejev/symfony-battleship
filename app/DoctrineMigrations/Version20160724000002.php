<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160724000002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE player_sessions (id INTEGER NOT NULL, player INTEGER NOT NULL, hash VARCHAR(40) NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CB7DCF398197A65 ON player_sessions (player)');
        $this->addSql('CREATE INDEX INDEX_SESSION_HASH ON player_sessions (hash)');
        $this->addSql('DROP INDEX INDEX_BATTLEFIELDS_PLAYER');
        $this->addSql('DROP INDEX INDEX_BATTLEFIELDS_GAME');
        $this->addSql('CREATE TEMPORARY TABLE __temp__battlefields AS SELECT id, game, player FROM battlefields');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('CREATE TABLE battlefields (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO battlefields (id, game, player) SELECT id, game, player FROM __temp__battlefields');
        $this->addSql('DROP TABLE __temp__battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_PLAYER ON battlefields (player)');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_GAME ON battlefields (game)');
        $this->addSql('DROP INDEX UNIQUE_CELL_PER_BATTLEFIELD');
        $this->addSql('DROP INDEX INDEX_CELLS_BATTLEFIELD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cells AS SELECT id, battlefield, coordinate, flags FROM cells');
        $this->addSql('DROP TABLE cells');
        $this->addSql('CREATE TABLE cells (id INTEGER NOT NULL, battlefield INTEGER NOT NULL, coordinate VARCHAR(3) NOT NULL COLLATE BINARY, flags INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cells (id, battlefield, coordinate, flags) SELECT id, battlefield, coordinate, flags FROM __temp__cells');
        $this->addSql('DROP TABLE __temp__cells');
        $this->addSql('CREATE UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD ON cells (battlefield, coordinate)');
        $this->addSql('CREATE INDEX INDEX_CELLS_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('DROP INDEX INDEX_GAME_RESULT_WINNER');
        $this->addSql('DROP INDEX INDEX_GAME_RESULT_GAME');
        $this->addSql('DROP INDEX UNIQ_A619B3B232B318C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_results AS SELECT id, game, player, timestamp FROM game_results');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('CREATE TABLE game_results (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A619B3B232B318C FOREIGN KEY (game) REFERENCES games (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A619B3B98197A65 FOREIGN KEY (player) REFERENCES players (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game_results (id, game, player, timestamp) SELECT id, game, player, timestamp FROM __temp__game_results');
        $this->addSql('DROP TABLE __temp__game_results');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_WINNER ON game_results (player)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_GAME ON game_results (game)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A619B3B232B318C ON game_results (game)');
        $this->addSql('DROP INDEX INDEX_PLAYER_NAME');
        $this->addSql('CREATE TEMPORARY TABLE __temp__players AS SELECT id, name, flags FROM players');
        $this->addSql('DROP TABLE players');
        $this->addSql('CREATE TABLE players (id INTEGER NOT NULL, flags INTEGER NOT NULL, email VARCHAR(25) NOT NULL, passwordHash VARCHAR(40) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO players (id, email, flags) SELECT id, name, flags FROM __temp__players');
        $this->addSql('DROP TABLE __temp__players');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL ON players (email)');
        $this->addSql('CREATE INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD ON players (email, passwordHash)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE player_sessions');
        $this->addSql('DROP INDEX INDEX_BATTLEFIELDS_GAME');
        $this->addSql('DROP INDEX INDEX_BATTLEFIELDS_PLAYER');
        $this->addSql('CREATE TEMPORARY TABLE __temp__battlefields AS SELECT id, game, player FROM battlefields');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('CREATE TABLE battlefields (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO battlefields (id, game, player) SELECT id, game, player FROM __temp__battlefields');
        $this->addSql('DROP TABLE __temp__battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_GAME ON battlefields (game)');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_PLAYER ON battlefields (player)');
        $this->addSql('DROP INDEX INDEX_CELLS_BATTLEFIELD');
        $this->addSql('DROP INDEX UNIQUE_CELL_PER_BATTLEFIELD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cells AS SELECT id, battlefield, coordinate, flags FROM cells');
        $this->addSql('DROP TABLE cells');
        $this->addSql('CREATE TABLE cells (id INTEGER NOT NULL, battlefield INTEGER NOT NULL, coordinate VARCHAR(3) NOT NULL, flags INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO cells (id, battlefield, coordinate, flags) SELECT id, battlefield, coordinate, flags FROM __temp__cells');
        $this->addSql('DROP TABLE __temp__cells');
        $this->addSql('CREATE INDEX INDEX_CELLS_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('CREATE UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD ON cells (battlefield, coordinate)');
        $this->addSql('DROP INDEX UNIQ_A619B3B232B318C');
        $this->addSql('DROP INDEX INDEX_GAME_RESULT_GAME');
        $this->addSql('DROP INDEX INDEX_GAME_RESULT_WINNER');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_results AS SELECT id, game, player, timestamp FROM game_results');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('CREATE TABLE game_results (id INTEGER NOT NULL, game INTEGER NOT NULL, player INTEGER NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO game_results (id, game, player, timestamp) SELECT id, game, player, timestamp FROM __temp__game_results');
        $this->addSql('DROP TABLE __temp__game_results');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A619B3B232B318C ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_GAME ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_WINNER ON game_results (player)');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL');
        $this->addSql('DROP INDEX INDEX_PLAYER_EMAIL_AND_PASSWORD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__players AS SELECT id, email, flags FROM players');
        $this->addSql('DROP TABLE players');
        $this->addSql('CREATE TABLE players (id INTEGER NOT NULL, flags INTEGER NOT NULL, name VARCHAR(25) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO players (id, name, flags) SELECT id, email, flags FROM __temp__players');
        $this->addSql('DROP TABLE __temp__players');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
    }
}
