<?php

class FormFieldAlter extends qFormField
{
    public $tagAttrib = array('class', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_ALTER, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'onclick');
        $this->setItem('link', '');
        $this->setItem('primary', false);
    }

    public function html(): string
    {
        $opt = $this->getItems();
        $opt['class'] .= ' btn';
        if (true === $opt['primary']) {
            $opt['class'] .= ' btn-primary';
        }
        
        $link = $this->form()->preprocessValue($this->link);
        $opt['onclick'] = "qAnt.ajax.load('{$link}')";
        $html = '<input type="button" '.$this->getHtmlAttr($opt)." />\n";
        return $html;
    }
}
