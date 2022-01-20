<?php

class FormFieldConfirm extends qFormField
{
    public $tagAttrib = array('class', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_CONFIRM, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'onclick');
        //$this->setItem('onclick', '');
    }

    public function html(): string
    {
        $this->onclick = "qAnt.confirm('{$this->id}-wrapper','{$this->link}','{$this->value}','{$this->confirmText}','{$this->cancelText}')";
        $html = '';
        $html .= '<input class="btn" type="button" ';
        $html .= $this->getHtmlAttr($this->items());
        $html .= " />\n";
        //$html .= $this->htmlWrapperEnd();
        return $html;
    }
}
