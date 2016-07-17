<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160717005000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE battlefields (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, player INT NOT NULL, INDEX INDEX_BATTLEFIELDS_GAME (game), INDEX INDEX_BATTLEFIELDS_PLAYER (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cells (id INT AUTO_INCREMENT NOT NULL, battlefield INT NOT NULL, coordinate VARCHAR(3) NOT NULL, flags INT NOT NULL, INDEX INDEX_CELLS_BATTLEFIELD (battlefield), UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD (battlefield, coordinate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_results (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, player INT NOT NULL, timestamp DATETIME NOT NULL, UNIQUE INDEX UNIQ_A619B3B232B318C (game), INDEX INDEX_GAME_RESULT_GAME (game), INDEX INDEX_GAME_RESULT_WINNER (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, flags INT NOT NULL, INDEX INDEX_PLAYER_NAME (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3B98197A65 FOREIGN KEY (player) REFERENCES players (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->skipIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3B232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3B98197A65');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('DROP TABLE cells');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('DROP TABLE players');
    }
}
