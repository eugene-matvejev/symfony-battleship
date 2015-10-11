<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151011154641 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA682E7DC2B');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6EC55B7A4');
        $this->addSql('DROP INDEX IDX_EDE65EA682E7DC2B ON battlefields');
        $this->addSql('DROP INDEX IDX_EDE65EA6EC55B7A4 ON battlefields');
        $this->addSql('ALTER TABLE battlefields ADD player INT DEFAULT NULL, ADD game INT DEFAULT NULL, DROP playerId, DROP gameId');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA698197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6232B318C FOREIGN KEY (game) REFERENCES games (id)');
        $this->addSql('CREATE INDEX IDX_EDE65EA698197A65 ON battlefields (player)');
        $this->addSql('CREATE INDEX IDX_EDE65EA6232B318C ON battlefields (game)');
        $this->addSql('ALTER TABLE cells ADD state INT DEFAULT NULL, ADD battlefield INT DEFAULT NULL, DROP stateId, DROP battlefieldId');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD8A393D2FB FOREIGN KEY (state) REFERENCES cellState (id)');
        $this->addSql('ALTER TABLE cells ADD CONSTRAINT FK_55C1CBD851B7F6D5 FOREIGN KEY (battlefield) REFERENCES battlefields (id)');
        $this->addSql('CREATE INDEX IDX_55C1CBD8A393D2FB ON cells (state)');
        $this->addSql('CREATE INDEX IDX_55C1CBD851B7F6D5 ON cells (battlefield)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA698197A65');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6232B318C');
        $this->addSql('DROP INDEX IDX_EDE65EA698197A65 ON battlefields');
        $this->addSql('DROP INDEX IDX_EDE65EA6232B318C ON battlefields');
        $this->addSql('ALTER TABLE battlefields ADD playerId INT DEFAULT NULL, ADD gameId INT DEFAULT NULL, DROP player, DROP game');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA682E7DC2B FOREIGN KEY (playerId) REFERENCES players (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6EC55B7A4 FOREIGN KEY (gameId) REFERENCES games (id)');
        $this->addSql('CREATE INDEX IDX_EDE65EA682E7DC2B ON battlefields (playerId)');
        $this->addSql('CREATE INDEX IDX_EDE65EA6EC55B7A4 ON battlefields (gameId)');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD8A393D2FB');
        $this->addSql('ALTER TABLE cells DROP FOREIGN KEY FK_55C1CBD851B7F6D5');
        $this->addSql('DROP INDEX IDX_55C1CBD8A393D2FB ON cells');
        $this->addSql('DROP INDEX IDX_55C1CBD851B7F6D5 ON cells');
        $this->addSql('ALTER TABLE cells ADD stateId INT NOT NULL, ADD battlefieldId INT NOT NULL, DROP state, DROP battlefield');
    }
}
