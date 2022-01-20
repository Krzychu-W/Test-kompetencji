<?php

class FormFieldAbutton extends qFormField
{
    public $tagAttrib = array('onchange', 'class', 'onclick');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_BUTTON, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'onclick');
        //$this->setItem('onclick', '');
        //$this->setItem('span', '3');
        $this->setItem('woWrapper', false);
    }

    public function html(): string
    {
        $opt = $this->getItems();
        $opt['class'] .= ' btn';
        if ($this->asWrapper) {
            $html = '<td>';
        } else {
            if (true !== $this->woWrapper) {
                $html = $this->htmlWrapper();
                $html .= $this->htmlLabel();
            }
        }
        $html .= '<button';
        $html .= $this->getHtmlAttr($opt);
        $html .= ' />'.$opt['value']."</button>\n";
        if ($this->asWrapper) {
            $html .= '<p>'.$this->htmlLabelSmall().'</p>';
            $html .= '</td>';
        } else {
            if (true !== $this->woWrapper) {
                $html .= $this->htmlDescription();
                $html .= $this->htmlWrapperEnd();
            }
        }

        return $html;
    }
}
