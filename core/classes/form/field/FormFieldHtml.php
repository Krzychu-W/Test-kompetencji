<?php

/**
 * Class FormFieldHtml
 *
 * @property $block Komunikat o błędzie
 */
class FormFieldHtml extends qFormField
{
    public $tagAttrib = array();

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_HTML, $fieldAttr);
        $this->setHtmlAttrib();
        $this->block = '';
        $this->setItem('class', $this->class);
        $this->setItem('tag', 'td');
    }

    public function html(): string
    {
        $html = $this->htmlWrapper();
        $html .= $this->block;
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function isValue()
    {
        // czy to pole zwraca wartość
        return false;
    }
}
