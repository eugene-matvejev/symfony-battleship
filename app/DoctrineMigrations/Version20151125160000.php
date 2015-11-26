<?php

namespace Application\Migrations;

use AppBundle\Model\CellModel;
use AppBundle\Model\PlayerModel;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151125160000 extends AbstractMigration
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
                playerType
            VALUES
                ('. PlayerModel::TYPE_CPU .', "Player controlled by computer"),
                ('. PlayerModel::TYPE_HUMAN .', "Player controlled by human");
        ');
        $this->addSql('
            INSERT INTO
                players
            VALUES
                (NULL, '. PlayerModel::TYPE_CPU .', "CPU");
        ');
        $this->addSql('
            INSERT INTO
                cellState
            VALUES
                ('. CellModel::STATE_WATER_LIVE .', "untouched water"),
                ('. CellModel::STATE_WATER_DIED .', "shooted water"),
                ('. CellModel::STATE_SHIP_LIVE .', "live ship"),
                ('. CellModel::STATE_SHIP_DIED .', "damaged ship");
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            DELETE FROM
                players
            WHERE
                name = "CPU" AND type = '. PlayerModel::TYPE_CPU .';
        ');
        $this->addSql('
            DELETE FROM
                playerType
            WHERE
                type IN ('. PlayerModel::TYPE_CPU .', '. PlayerModel::TYPE_HUMAN .');
        ');
        $this->addSql('
            DELETE FROM
                cellState
            WHERE
                id IN ('. join(',', CellModel::getAllStates()) .');
        ');
    }
}
