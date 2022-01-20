<?php

class FormFieldAppend extends qFormField
{
    public $tagAttrib = array('class', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_BUTTON, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'onclick');
    }

    public function html(): string
    {
        $opt = $this->getItems();
        $opt['class'] .= ' input-append';
        $html = '<div';
        $html .= $this->getHtmlAttr($opt);
        $html .= " >\n";
        foreach ($this->fields()->getItems() as $item) {
            $html .= $item->html();
        }
        $html .= "</div>\n";

        return $html;
    }
}
