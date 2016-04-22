<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160416000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD8A393D2FB');
        $this->addSql('DROP TABLE cell_states');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('DROP INDEX index_battlefield_game ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_GAME ON battlefields (game)');
        $this->addSql('DROP INDEX index_battlefield_player ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELDS_PLAYER ON battlefields (player)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('DROP INDEX IDX_55C1CBD8A393D2FB ON cells');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE cells CHANGE state mask INT NOT NULL');
        $this->addSql('DROP INDEX index_cell_battlefield ON cells');
        $this->addSql('CREATE INDEX INDEX_CELLS_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('DROP INDEX index_battlefield_unique_cell ON cells');
        $this->addSql('CREATE UNIQUE INDEX UNIQUE_CELL_PER_BATTLEFIELD ON cells (battlefield, coordinate)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cell_states (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('DROP INDEX index_battlefields_game ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELD_GAME ON battlefields (game)');
        $this->addSql('DROP INDEX index_battlefields_player ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELD_PLAYER ON battlefields (player)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('ALTER TABLE cells CHANGE mask state INT NOT NULL');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD8A393D2FB FOREIGN KEY (state) REFERENCES cell_states (id)');
        $this->addSql('CREATE INDEX IDX_55C1CBD8A393D2FB ON cells (state)');
        $this->addSql('DROP INDEX unique_cell_per_battlefield ON cells');
        $this->addSql('CREATE UNIQUE INDEX INDEX_BATTLEFIELD_UNIQUE_CELL ON cells (battlefield, coordinate)');
        $this->addSql('DROP INDEX index_cells_battlefield ON cells');
        $this->addSql('CREATE INDEX INDEX_CELL_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
    }
}
