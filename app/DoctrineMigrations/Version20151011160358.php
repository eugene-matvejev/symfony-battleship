<?php

namespace Application\Migrations;

use AppBundle\Model\CellModel;
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            INSERT INTO
                cellState
            VALUES
                ('. CellModel::STATE_WATER_LIVE .', "untouched water"),
                ('. CellModel::STATE_WATER_DIED .', "shooted water"),
                ('. CellModel::STATE_SHIP_LIVE .', "live ship"),
                ('. CellModel::STATE_SHIP_DIED .', "damaged ship");');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
