<?php

class FormFieldActions extends qFormField
{
    public $tagAttrib = array('class', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_BUTTON, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'onclick');
    }

    public function html(): string
    {
        $html = '';
        $opt = $this->getItems();
        $opt['class'] .= ' form-actions';
        $html .= '<div';
        $html .= $this->getHtmlAttr($opt);
        $html .= " >\n";
        foreach ($this->fields()->getItems() as $item) {
            if ($item->display()) {
                $html .= $item->html();
            }
        }
        $html .= "</div>\n";

        return $html;
    }
}
