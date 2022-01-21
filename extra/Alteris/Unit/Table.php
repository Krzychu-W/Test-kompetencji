<?php
/**
 * Tabela obsługi jednostek miar
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
namespace Alteris\Unit;


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
        $this->setTable('unit', 'autoincrement');
        $this->addField('name', 'type:Varchar;size:24;default:');
        $this->addField('short', 'type:Varchar;size:4;default:');
        $this->addIndex('name', ['name']);
    }

    /**
     * Pobranie rekordu
     *
     * @return \Alteris\Unit\Record
     */
    private function objRecord() {
        return new \Alteris\Unit\Record($this);
    }

    /**
     * Zwraca listę obiektów \Alteris\Model\Record
     *
     * @return array
     */
    public function getAllRecords() {
        $sql = "SELECT * FROM `unit` ORDER BY `name`";
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $result[$row->id] = $this->rowRecord($row);
        }
        return $result;
    }

    /**
     * Zwraca id rekordu w/g nazwy
     *
     * @param string $name
     * @return bool|integer
     */
    public function getIdByName(string $name)
    {
        $sql = "SELECT id FROM `unit` WHERE `name` = '{$name}'";

        return \qDb::connect()->select($sql)->result();
    }




}