<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160102125715 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('DROP INDEX idx_ede65ea6232b318c ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELD_GAME ON battlefields (game)');
        $this->addSql('DROP INDEX idx_ede65ea698197a65 ON battlefields');
        $this->addSql('CREATE INDEX INDEX_BATTLEFIELD_PLAYER ON battlefields (player)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('DROP INDEX idx_55c1cbd851b7f6d5 ON cells');
        $this->addSql('CREATE INDEX INDEX_CELL_BATTLEFIELD ON cells (battlefield)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BECF6600E');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_GAME ON gamesResults (game)');
        $this->addSql('DROP INDEX idx_6b05d5becf6600e ON gamesResults');
        $this->addSql('CREATE INDEX INDEX_GAME_RESULT_WINNER ON gamesResults (winner)');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BECF6600E FOREIGN KEY (winner) REFERENCES players (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('DROP INDEX index_battlefield_game ON battlefields');
        $this->addSql('CREATE INDEX IDX_EDE65EA6232B318C ON battlefields (game)');
        $this->addSql('DROP INDEX index_battlefield_player ON battlefields');
        $this->addSql('CREATE INDEX IDX_EDE65EA698197A65 ON battlefields (player)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('DROP INDEX index_cell_battlefield ON cells');
        $this->addSql('CREATE INDEX IDX_55C1CBD851B7F6D5 ON cells (battlefield)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('DROP INDEX INDEX_GAME_RESULT_GAME ON gamesResults');
        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BECF6600E');
        $this->addSql('DROP INDEX index_game_result_winner ON gamesResults');
        $this->addSql('CREATE INDEX IDX_6B05D5BECF6600E ON gamesResults (winner)');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BECF6600E FOREIGN KEY (winner) REFERENCES players (id)');
    }
}
