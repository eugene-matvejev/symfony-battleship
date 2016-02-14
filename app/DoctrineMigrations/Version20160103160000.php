<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;

class Version20160103160000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('
            INSERT INTO
                player_types
            VALUES
                (' . PlayerModel::TYPE_CPU . ', "Player controlled by computer"),
                (' . PlayerModel::TYPE_HUMAN . ', "Player controlled by human");
        ');
        $this->addSql('
            INSERT INTO
                players
            VALUES
                (NULL, ' . PlayerModel::TYPE_CPU . ', "CPU");
        ');
        $this->addSql('
            INSERT INTO
                cell_states
            VALUES
                (' . CellModel::STATE_WATER_LIVE . ', "untouched water"),
                (' . CellModel::STATE_WATER_DIED . ', "shooted water"),
                (' . CellModel::STATE_SHIP_LIVE . ', "live ship"),
                (' . CellModel::STATE_SHIP_DIED . ', "damaged ship"),
                (' . CellModel::STATE_WATER_SKIP . ', "water skip");
        ');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('
            DELETE FROM
                players
            WHERE
                name = "CPU" AND type = ' . PlayerModel::TYPE_CPU . ';
        ');
        $this->addSql('
            DELETE FROM
                player_types
            WHERE
                type IN (' . PlayerModel::TYPE_CPU . ', ' . PlayerModel::TYPE_HUMAN . ');
        ');
        $this->addSql('
            DELETE FROM
                cell_states
            WHERE
                id IN (' . join(',', CellModel::getAllStates()) . ');
        ');
    }
}
