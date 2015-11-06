<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151104104820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE battlefields (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, player INT NOT NULL, INDEX IDX_EDE65EA6232B318C (game), INDEX IDX_EDE65EA698197A65 (player), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cells (id INT AUTO_INCREMENT NOT NULL, state INT NOT NULL, battlefield INT NOT NULL, x INT NOT NULL, y INT NOT NULL, INDEX IDX_55C1CBD8A393D2FB (state), INDEX IDX_55C1CBD851B7F6D5 (battlefield), UNIQUE INDEX axisXY (battlefield, x, y), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cellState (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, result INT DEFAULT NULL, timestamp DATETIME NOT NULL, UNIQUE INDEX UNIQ_FF232B31136AC113 (result), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gamesResults (id INT AUTO_INCREMENT NOT NULL, game INT NOT NULL, winner INT NOT NULL, timestamp DATETIME NOT NULL, UNIQUE INDEX UNIQ_6B05D5BE232B318C (game), INDEX IDX_6B05D5BECF6600E (winner), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, name VARCHAR(200) NOT NULL, INDEX IDX_264E43A68CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playerType (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD8A393D2FB FOREIGN KEY (state) REFERENCES cellState (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31136AC113 FOREIGN KEY (result) REFERENCES gamesResults (id)');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BE232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BECF6600E FOREIGN KEY (winner) REFERENCES players (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A68CDE5729 FOREIGN KEY (type) REFERENCES playerType (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD8A393D2FB');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BE232B318C');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31136AC113');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BECF6600E');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A68CDE5729');
        $this->addSql('DROP TABLE battlefields');
        $this->addSql('DROP TABLE cells');
        $this->addSql('DROP TABLE cellState');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE gamesResults');
        $this->addSql('DROP TABLE players');
        $this->addSql('DROP TABLE playerType');
    }
}
