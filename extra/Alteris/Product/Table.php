<?php
/**
 * Tabela obsługi jednostek miar
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
namespace Alteris\Product;


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
        $this->setTable('product', 'autoincrement');
        $this->addField('indeks', 'type:Varchar;size:24;default:');
        $this->addField('name', 'type:Varchar;size:128;default:');
        $this->addField('unit_id', 'type:Integer;attribs:UNSIGNED;default:0');
        $this->addField('group_id', 'type:Integer;attribs:UNSIGNED;default:0');
        $this->addUnique('indeks', ['indeks']);
        $this->addIndex('name', ['name']);
        $this->addIndex('unit_id', ['unit_id']);
        $this->addIndex('group_id', ['group_id']);
    }

    /**
     * Pobranie rekordu
     *
     * @return \Alteris\Product\Record
     */
    protected function objRecord() {
        return new \Alteris\Product\Record($this);
    }

    /**
     * Zwraca listę obiektów \Alteris\Product\Record
     *
     * @return array
     */
    public function getAllRecords():array
    {
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
     * @param string $indeks
     * @return bool|integer
     */
    public function getIdByIndeks(string $indeks)
    {
        $sql = "SELECT id FROM `product` WHERE `indeks` = '{$indeks}'";

        return \qDb::connect()->select($sql)->result();
    }




}