<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160103150000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('CREATE TABLE battlefields (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, player INT NOT NULL, INDEX INDEX_BATTLEFIELD_GAME (game), INDEX INDEX_BATTLEFIELD_PLAYER (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cells (id INT AUTO_INCREMENT NOT NULL, battlefield INT NOT NULL, state INT NOT NULL, x INT NOT NULL, y INT NOT NULL, INDEX IDX_55C1CBD8A393D2FB (state), INDEX INDEX_CELL_BATTLEFIELD (battlefield), UNIQUE INDEX axisXY (battlefield, x, y), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cell_states (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_results (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, player INT NOT NULL, timestamp DATETIME NOT NULL, UNIQUE INDEX UNIQ_A619B3B232B318C (game), INDEX INDEX_GAME_RESULT_GAME (game), INDEX INDEX_GAME_RESULT_WINNER (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, name VARCHAR(200) NOT NULL, INDEX IDX_264E43A68CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD8A393D2FB FOREIGN KEY (state) REFERENCES cell_states (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B98197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A68CDE5729 FOREIGN KEY (type) REFERENCES player_types (id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql"');

        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD8A393D2FB');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3B232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3B98197A65');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A68CDE5729');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('DROP TABLE cells');
        $this->addSql('DROP TABLE cell_states');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('DROP TABLE players');
        $this->addSql('DROP TABLE player_types');
    }
}