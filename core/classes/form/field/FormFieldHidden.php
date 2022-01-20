<?php

class FormFieldHidden extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_HIDDEN, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'type');
    }

    public function html(): string
    {
        $html = '<input';
        $html .= $this->getHtmlAttr($this->getItems());
        $html .= " />\n";

        return $html;
    }
}
