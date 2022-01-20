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

    public function getAllRecords() {
        $sql = "SELECT * FROM `unit` ORDER BY `name`";
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $result[$row->id] = $this->rowRecord($row);
        }
        return $result;
    }

}