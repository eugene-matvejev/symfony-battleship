<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151024124718 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BE98197A65');
        $this->addSql('DROP INDEX IDX_6B05D5BE98197A65 ON gamesResults');
        $this->addSql('ALTER TABLE gamesResults CHANGE player winner INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BECF6600E FOREIGN KEY (winner) REFERENCES players (id)');
        $this->addSql('CREATE INDEX IDX_6B05D5BECF6600E ON gamesResults (winner)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gamesResults DROP FOREIGN KEY FK_6B05D5BECF6600E');
        $this->addSql('DROP INDEX IDX_6B05D5BECF6600E ON gamesResults');
        $this->addSql('ALTER TABLE gamesResults CHANGE winner player INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gamesResults ADD CONSTRAINT FK_6B05D5BE98197A65 FOREIGN KEY (player) REFERENCES players (id)');
        $this->addSql('CREATE INDEX IDX_6B05D5BE98197A65 ON gamesResults (player)');
    }
}
