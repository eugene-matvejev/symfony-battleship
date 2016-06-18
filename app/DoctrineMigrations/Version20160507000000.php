<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160507000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on "postgresql"');

        $this->addSql('CREATE TABLE battlefields (id SERIAL NOT NULL, game INT NOT NULL, player INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_GAME ON battlefields (game)');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_PLAYER ON battlefields (player)');
        $this->addSql('CREATE TABLE cells (id SERIAL NOT NULL, battlefield INT NOT NULL, coordinate VARCHAR(3) NOT NULL, flags INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_CELLS_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('CREATE UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD ON cells (battlefield, coordinate)');
        $this->addSql('CREATE TABLE games (id SERIAL NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game_results (id SERIAL NOT NULL, game INT NOT NULL, player INT NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A619B3B232B318C ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_GAME ON game_results (game)');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_WINNER ON game_results (player)');
        $this->addSql('CREATE TABLE players (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, flags INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX INDEX_PLAYER_NAME ON players (name)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B232B318C FOREIGN KEY (game) REFERENCES games (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B98197A65 FOREIGN KEY (player) REFERENCES players (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema)
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on "postgresql"');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cells DROP CONSTRAINT FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE battlefields DROP CONSTRAINT FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE game_results DROP CONSTRAINT FK_A619B3B232B318C');
        $this->addSql('ALTER TABLE battlefields DROP CONSTRAINT FK_EDE65EA698197A65');
        $this->addSql('ALTER TABLE game_results DROP CONSTRAINT FK_A619B3B98197A65');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('DROP TABLE cells');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('DROP TABLE players');
    }
}
