<?php

namespace AppBundle\Library\AI;

use AppBundle\Entity\CellEntity;
use AppBundle\Model\CellStateModel;

class Core {

    /**
     * @var CellStateModel
     */
    private $cellStateModel;

    /**
     * @var boolean
     */
    private $turnDone;

    /**
     * @param CellStateModel $cellStateModel
     *
     * @return $this
     */
    public function setCellStateModel(CellStateModel $cellStateModel)
    {
        $this->cellStateModel = $cellStateModel;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTurnDone()
    {
        return $this->turnDone;
    }

    /**
     * @param boolean $boolean
     *
     * @return $this
     */
    public function setTurnDone($boolean)
    {
        $this->turnDone = (bool)$boolean;

        return $this;
    }

    /**
     * @param CellEntity $cell
     */
    public function turn(CellEntity $cell)
    {
        if(in_array($cell->getState()->getId(), [CellStateModel::WATER_LIVE, CellStateModel::SHIP_LIVE])) {
            $this->cellStateModel->swapStatus($cell);
            $this->setTurnDone(true);
        }
    }
}