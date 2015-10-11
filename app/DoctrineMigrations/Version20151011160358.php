<?php

namespace Application\Migrations;

use AppBundle\Model\CellStateModel;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151011160358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            INSERT INTO
                cellState
            VALUES
                ('. CellStateModel::WATER_LIVE .', "untouched water"),
                ('. CellStateModel::WATER_DIED .', "shooted water"),
                ('. CellStateModel::SHIP_LIVE .', "live ship"),
                ('. CellStateModel::SHIP_DIED .', "damaged ship");');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
