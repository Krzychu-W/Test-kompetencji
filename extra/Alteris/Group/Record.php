<?php
namespace Alteris\Group;

class Record extends \Alteris\Model\Record
{

    /**
     * Zapis rekordu i porządkowanie
     *
     * @return int
     */
    public function save():int {
        $id = parent::save();
        $this->getTable()->resetHierarchy();

        return $id;
    }

    public function getChildren() {

        return $this->getTable()->getCheldern($this);
    }




}

