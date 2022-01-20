<?php

class FormFieldForm extends qFormField
{
    public $tagAttrib = array('class');

    /**
     * Utwórz nową stronę.
     */
    public function __construct()
    {
        parent::__construct(qFormField::TYPE_FORM);
    }

    public function html(): string
    {
        $html = "<input type=\"{$this->type}\" id=\"{$this->id}\" name=\"{$this->name}\" value=\"{$this->value}\"";
        $html .= $this->getAttrib($this->items());
        $html .= ' />';
        $this->html = $html;

        return $html;
    }
}
