<?php

class FormFieldValue extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_VALUE, $formAttr);
    }

    public function html(): string
    {
        return '';
    }
}
