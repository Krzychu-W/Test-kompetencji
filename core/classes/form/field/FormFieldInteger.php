<?php

class FormFieldInteger extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_INTEGER, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'size', 'value', 'onchange');
        $this->setItem('size', '10');
    }

    public function _html(): string
    {
        $opt = $this->items();
        if (false !== $this->span) {
            $opt['class'] .= ' span'.$this->span;
        }
        $html = '<div class="field-line">';
        $html .= $this->in_line_prefix.'<input type="text"';
        $html .= $this->getHtmlAttr($opt);
        $html .= " />{$this->in_line_suffix}\n";
        $html .= "</div>\n";

        return $html;
    }
}
