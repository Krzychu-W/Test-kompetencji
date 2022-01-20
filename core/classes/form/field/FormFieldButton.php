<?php

class FormFieldButton extends qFormField
{
    public $tagAttrib = array('onchange', 'class', 'size', 'maxlength', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_BUTTON, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'type', 'onclick', 'data-id');
        //$this->setItem('onclick', '');
    }

    public function html(): string
    {
        $opt = $this->getItems();
        $link = $this->form()->preprocessValue($this->link);
        if (!empty($link)) {
            $opt['onclick'] = "qAnt.ajax.load('{$link}')";
        }
        $html = "{$this->in_line_prefix}<input type=\"button\" ".$this->getHtmlAttr($opt)." />{$this->in_line_suffix}\n";

        return $html;
    }
}
