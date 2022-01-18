<?php
namespace Alteris\Unit;

class Table extends \Alteris\Model\Table
{

    public function init() {
        $this->setTable('unit', 'autoincrement');
        $this->addField('name', 'type:Varchar;size:24;default:');
        $this->addIndex('name', ['name']);

    }

    /**
     * @return \Alteris\Unit\Record
     */
    public function objRecord() {
        return new \Alteris\Unit\Record($this);
    }

}