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

    public function getOptions($id = 0) {
        $sql = "SELECT * FROM `group` ORDER BY `name`";
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $result[$row->id] = $this->rowRecord($row);
        }
        return $result;

        $options = [];
        if ($empty) {
            $options[0] = $empty;
        }
        foreach ($this->getAllRows() as $row) {
            $options[$row->id] = $row->name;
        }

        return $options;


    }





}