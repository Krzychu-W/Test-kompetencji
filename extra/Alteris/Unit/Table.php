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
    protected function objRecord() {
        return new \Alteris\Unit\Record($this);
    }

    /**
     * Zwraca wiersze w postaci array of stdClass
     *
     * @return array
     */
    public function getAllRows():array
    {
        $sql = "SELECT * FROM `unit` ORDER BY `name`";

        return \qDb::connect()->select($sql)->rows();
    }

    /**
     * Zwraca listę obiektów \Alteris\Model\Record
     *
     * @return array
     */
    public function getAllRecords(): array
    {
        $result = [];
        foreach ($this->getAllRows() as $row) {
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

    /**
     * Zwraca tablicę asocjacyjną do formularza select
     *
     * @param string $empty - wartość w nagłówku
     * @return array
     */
    public function getOptions(string $empty = ''):array
    {
        $options = [];
        if ($empty) {
            $options[0] = $empty;
        }
        foreach ($this->getAllRows() as $row) {
            $options[$row->id] = $row->name;
        }

        return $options;
    }

    /**
     * Usuwanie rekordu z zabezpieczeniem.
     * Nie usuwa, jeżeli jest używany
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id): bool
    {
        $record = $this->getRecord($id);

        \qLog::write($this, $record);

        if ($record) {
            if ($record->isUsed()) {

                return false;
            }
        }

        return parent::delete($id);
    }


}