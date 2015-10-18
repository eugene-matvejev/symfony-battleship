<?php

namespace AppBundle\Repository;

use AppBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * Class PlayerRepository
 * @package AppBundle\Repository
 */
class PlayerTypeRepository extends EntityRepository {
    public function getTypes() {
        return [
            PlayerModel::TYPE_CPU => $this->findOneBy(['id' => PlayerModel::TYPE_CPU]),
            PlayerModel::TYPE_HUMAN => $this->findOneBy(['id' => PlayerModel::TYPE_HUMAN])
        ];
    }
}