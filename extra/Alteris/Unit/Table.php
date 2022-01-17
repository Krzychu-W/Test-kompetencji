<?php
namespace Alteris\Unit;

class Table extends \Alteris\Model\Table
{

    public function init() {
        $this->setTable('unit', 'autoincrement');
        $this->addField('name', 'type:Varchar;size:24');
        $this->addIndex('name', ['name']);

    }

}