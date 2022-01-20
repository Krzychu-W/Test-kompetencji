<?php

class FormFieldSubmit extends qFormField
{
    /**
     * Enter description here ...
     *
     * @param string $fieldAttr
     */
    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_SUBMIT, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'onclick', 'value', 'type');
        $this->setItem('primary', false);
        $this->setItem('isHidden', false);
    }

    public function html(): string
    {
        //$html  = $this->htmlWrapper();
        //$html .= $this->htmlLabel();
        $html = '';
        $html .= $this->_html();
        $html .= $this->htmlDescription();
        //$html .= $this->htmlWrapperEnd();
        return $html;
    }

    public function _html(): string
    {
        $opt = $this->items();
        $opt['class'] .= ' btn';
        if (true === $opt['primary']) {
            $opt['class'] .= ' btn-primary';
        }
        $html = '';
        $html .= "{$this->in_line_prefix}<input ";
        if ($this->isHidden) {
            $html .= 'style="display:none;"';
        }
        $html .= $this->getHtmlAttr($opt);
        $html .= " >{$this->in_line_suffix}\n";

        return $html;
    }
}
