<?php
namespace Alteris\Unit;

/**
 * Rekord jednostki miar
 *
 * @property $id
 * @property $name
 * @property $short
 */
class Record extends \Alteris\Model\Record
{

    /**
     * Czy jednostka miar jest użyta
     *
     * @return bool
     */
    public function isUsed():bool
    {
        $sql = "SELECT count(*) FROM `product` AS B WHERE B.`unit_id` = '{$this->id}'";

        return (\qDb::connect()->select($sql)->result() > 0);
    }


}

