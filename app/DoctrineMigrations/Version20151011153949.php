<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151011153949 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields ADD playerId INT DEFAULT NULL, ADD gameId INT DEFAULT NULL');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA682E7DC2B FOREIGN KEY (playerId) REFERENCES players (id)');
        $this->addSql('ALTER TABLE battlefields ADD CONSTRAINT FK_EDE65EA6EC55B7A4 FOREIGN KEY (gameId) REFERENCES games (id)');
        $this->addSql('CREATE INDEX IDX_EDE65EA682E7DC2B ON battlefields (playerId)');
        $this->addSql('CREATE INDEX IDX_EDE65EA6EC55B7A4 ON battlefields (gameId)');
        $this->addSql('ALTER TABLE cells ADD stateId INT NOT NULL, ADD battlefieldId INT NOT NULL, DROP state, DROP battlefield');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA682E7DC2B');
        $this->addSql('ALTER TABLE battlefields DROP FOREIGN KEY FK_EDE65EA6EC55B7A4');
        $this->addSql('DROP INDEX IDX_EDE65EA682E7DC2B ON battlefields');
        $this->addSql('DROP INDEX IDX_EDE65EA6EC55B7A4 ON battlefields');
        $this->addSql('ALTER TABLE battlefields DROP playerId, DROP gameId');
        $this->addSql('ALTER TABLE cells ADD state INT NOT NULL, ADD battlefield INT NOT NULL, DROP stateId, DROP battlefieldId');
    }
}
