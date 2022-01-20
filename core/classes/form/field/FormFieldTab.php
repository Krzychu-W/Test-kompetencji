<?php

class FormFieldTab extends qFormField
{
    public $tagAttrib = array();

    public function __construct($form)
    {
        parent::__construct(qFormField::TYPE_TAB, $form);
        $this->setHtmlAttrib('id', 'class');
    }

    public function html(): string
    {
        $html = $this->htmlWrapper();
        //$html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= $this->_html();
        $html .= $this->htmlContentEnd();
        $html .= $this->htmlDescription();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function _html(): string
    {
        $html = '';
        foreach ($this->fields()->items() as $item) {
            $html .= $item->html();
        }

        return $html;
    }
}
