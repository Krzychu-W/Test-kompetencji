<?php

use Core\Javascript;

class FormFieldDatetime extends qFormField
{
    public $tagAttrib = array('onchange', 'class', 'size', 'maxlength');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_TEXT, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'placeholder', 'size', 'maxlength', 'value', 'type', 'readonly', 'onclick');
        $this->setItem('size', '25');
        $this->setItem('maxlength', '255');
        $this->setItem('span', '2');
        $this->setItem('dateformat', 'dd.mm.yy');
        $this->setItem('timeformat', false);
        $this->setItem('minDate', false);
    }

    public function html(): string
    {
        Javascript::renderReady('');
        return parent::html();
    }

    public function _html(): string
    {
        if (true === $this->dateformat) {
            $dateformat = 'yyyy-MM-dd';
        } else {
            $dateformat = $this->dateformat;
        }
        $opt = $this->items();
        if (false !== $this->span) {
            $opt['class'] .= ' span'.$this->span;
        }
        if (!isset($this->viewMode)) {
            $viewMode = 'days';
        } else {
            $viewMode = $this->viewMode;
        }
        $html = '<div class="field-line input-datetime input-append">';
        $html .= '<input data-format="'.$dateformat.'" ';
        $html .= $this->getHtmlAttr($opt);
        $html .= " />\n";
        $html .= "</div>\n";
        $atContent = $this->items();
        $html .= Javascript::renderReady('qAnt.module.formdatetime.init(\''.$atContent['id'].'\');');

        return $html;
    }
}
