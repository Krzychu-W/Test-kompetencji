<?php


class FormFieldText extends qFormField
{
    public $tagAttrib = array('onchange', 'class', 'size', 'maxlength');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_TEXT, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'placeholder', 'size', 'maxlength', 'value', 'type', 'readonly', 'onclick', 'onchange', 'onkeyup', 'placeholder', 'disabled', 'data-lang', 'style');
        $this->setItem('size', '25');
        $this->setItem('maxlength', '255');
        $this->setItem('col_class', false);
    }

    public function html(): string
    {
        $this->value = str_replace('"', '&quot;', $this->value);
        return parent::html();
    }

    public function _html(): string
    {
        $opt = $this->getItems();
        if ($this->disabled) {
            $opt['disabled'] .= 'disabled';
        }
        $html = '<div class="field-line '.(!empty($opt['append']) ? 'input-append' : '').'">';
        $html .= $this->in_line_prefix.'<input '.$this->getHtmlAttr($opt)." />{$this->in_line_suffix}\n";
        if (!empty($opt['append'])) {
            $html .= "<span class=\"add-on\">\n";
            $html .= "<i>\n";
            $html .= $opt['append'];
            $html .= "</i>\n";
            $html .= "</span>\n";
        }
        $html .= "</div>\n";

        return $html;
    }
}
