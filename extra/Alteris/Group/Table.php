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
        $this->addField('deep', 'type:Tinyint;attribs:UNSIGNED;default:0');
        $this->addField('hierarchy', 'type:Varchar;size:255;default:');
    }

    public function getRecord($id): object
    {
        if ($id == 0) {
            // wirtualny korzeń
            $record = $this->newRecord();
            $record->id = 0;
            $record->name = 'ROOT';
            return $record;
        }
        return parent::getRecord($id);
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
     * @param mixed $id - zakres
     * @param mixed $fromId - aktualna grupa (0 - nowa grupa)
     * @return array
     */
    public function getOptions($id = 0, $fromId = 0) : array
    {
        $notParent = false;
        if ($fromId) {
            $from = $this->getRecord($fromId);
            $notParent = $from->hierarchy;
        }
        $sql  = "SELECT A.*, (SELECT count(*) FROM `product` AS B WHERE B.group_id=A.id) as prods\n";
        $sql .= "  FROM `group` AS A\n";
        $sql .= " ORDER BY A.`hierarchy`";
        $result = [
            0 => [
                'label' => 'Root',
                'none' => false,
            ],
        ];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $str = str_repeat('-', $row->deep).' '.$row->name;
            $result[$row->id] = [
                'label' => $str,
                'none' => false,
            ];
            if ($fromId) {
                $cutHierarchy = substr($row->hierarchy, 0, strlen($notParent));
                if ($cutHierarchy === $notParent) {
                    // wykluczenie ze względu na zagłębienie
                    $result[$row->id]['none'] = true;
                }
            }
            if ($row->prods > 0) {
                // wykluczenie ze względu na podpięte produkty
                $result[$row->id]['none'] = true;
            }
        }

        return $result;
    }

    /**
     * Pobiera drzewo opcji, do których można podpiąć produkty
     *
     * @return array|array[]
     */
    public function getOptionsProd() : array
    {
        $sql  = "SELECT A.*,(SELECT count(*)-1 FROM `group` AS C WHERE C.hierarchy LIKE CONCAT(A.hierarchy ,'%')) as sub\n";
        $sql .= "  FROM `group` AS A\n";
        $sql .= " ORDER BY A.`hierarchy`";
        $result = [
            0 => [
                'label' => '(wybierz grupę)',
                'none' => true,
            ],
        ];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $str = str_repeat('-', $row->deep).' '.$row->name;
            $result[$row->id] = [
                'label' => $str,
                'none' => false,
            ];
            if ($row->sub > 0) {
                // wykluczenie ze względu na podpięte podgrupy
                $result[$row->id]['none'] = true;
            }
        }

        return $result;
    }

    /**
     * Porządkuje drzewo, w ramach gałęzi alfabetycznie
     * Funkcja rekurencyjna
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

    public function getCheldern($obj) {
        $deep = $obj->deep + 1;
        $hierarchy = $obj->hierarchy;
        if ($deep > 1) {
            $hierarchy .= '-';
            $sql = "SELECT * FROM `group` WHERE `hierarchy` LIKE '{$hierarchy}%' AND `deep` = '{$deep}' ORDER BY `name`";
        }
        else {
            $sql = "SELECT * FROM `group` WHERE `deep` = '{$deep}' ORDER BY `name`";
        }



        \qLog::write($deep,$hierarchy, $sql);
        $result = [];
        foreach (\qDb::connect()->select($sql)->rows() as $row) {
            $result[$row->id] = $this->rowRecord($row);
        }
        return $result;
    }


}