<?php
namespace Alteris\Group;
/**
 * Rekord grupy
 *
 * @property $id
 * @property $name
 * @property $deep
 * @property $hierarchy
 */

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

    /**
     * Pobranie BEZPOŚREDNICH potomków
     *
     * @return array of \Alteris\Group\Record
     */
    public function getChildren() {

        return $this->getTable()->getCheldern($this);
    }

    /**
     * Czy rekord może być usunięty
     *
     * @return bool
     */
    public function canDeleted():bool
    {
        if ($this->isNew()) {
            return false;
        }

        return $this->getTable()->canDeleted($this);
    }




}

