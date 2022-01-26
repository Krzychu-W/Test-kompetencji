<?php
/**
 * Tabela obsługi jednostek miar
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
namespace Alteris\Group;


class Table extends \Alteris\Model\Table
{

    /**
     * Definicja tabeli
     *
     * Definicja pozwala na wygenerowanie sql do stworzenia tabeli
     *   w oryginale pozwala dodatkowo przeprowadzać automatyczny update struktur,
     *   kluczy obcych i indeksów
     *   Tutaj obcięte do minimum.
     */
    public function init() {
        $this->setTable('group', 'autoincrement');
        $this->addField('name', 'type:Varchar;size:128;default:');
        $this->addField('parent_id', 'type:Integer;attribs:UNSIGNED;default:0');
    }

    /**
     * Pobranie rekordu
     *
     * @return \Alteris\Group\Record
     */
    protected function objRecord() {
        return new \Alteris\Group\Record($this);
    }

    /**
     * Zwraca listę obiektów \Alteris\Group\Record
     *
     * @return array
     */
    public function getAllRecords():array
    {
        $sql = "SELECT * FROM `group` ORDER BY `name`";
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $result[$row->id] = $this->rowRecord($row);
        }
        return $result;
    }

    /**
     * Pobiera drzewo opcji, do których można podpiąć grupy
     *
     * @param int $id
     * @return array
     */
    public function getOptions($id = 0) : array
    {
        $sql = "SELECT * FROM `group` ORDER BY `hierarchy`";
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $str = str_repeat('-', $row->deep).' '.$row->name;

            $result[$row->id] = $str;
        }

        return $result;
    }

    /**
     * Porządkuje drzewo, w ramach gałęzi alfabetycznie
     *
     * @param mixed $parent_id
     * @param string $prefix
     */
    public function resetHierarchy($parent_id = 0, string $prefix = '') {



        $sql = "SELECT * FROM `group` WHERE `parent_id` = '{$parent_id}' ORDER BY `name`";
        $lp = 0;
        if ($prefix !== '') {
            $prefix .= '-';
            $deep = intval(strlen($prefix) / 6) + 1;
        }
        else {
            $deep = 1;
        }
        $connect = \qDb::connect();
        foreach ($connect->select($sql)->rows() as $row) {
            $lp++;
            $hierarchy = $prefix . \qString::strPad0($lp, 5);
            if ($row->hierarchy !== $hierarchy || $row->deep != $deep) {
                $hSql = "UPDATE `group` SET `hierarchy` = '{$hierarchy}', `deep` = '{$deep}' WHERE `id` = {$row->id}";
                $connect->query($hSql);
            }
            $this->resetHierarchy($row->id, $hierarchy);
        }
    }


}