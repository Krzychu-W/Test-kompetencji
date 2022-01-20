<?php

class FormFieldCheckbox extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_CHECKBOX, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'namCome', 'type', 'checked', 'size', 'disabled', 'onclick', 'name');
        $this->setItem('data', 3);
    }

    public function html(): string
    {
        if (1 == $this->item('value')) {
            $this->setItem('checked', 'checked');
        }
        if ($this->asWrapper) {
            $html = '<td>';
        } else {
            $html = $this->htmlWrapper();
        }
        if ($this->asWrapper) {
            $id = $this->id;
            $html .= '<label class="checkbox" for="'.$id.'">';
            $html .= '<input type="hidden" name="'.$this->name.'"'.' value="0"/>'."\n";
            $html .= '<input'.$this->getHtmlAttr($this->items()).' '.$this->_getData().'value="1"/>'.$this->htmlLabelSmall()."\n";
            $html .= '</td>';
        } else {
            $id = $this->id;
            //$html .= '<label class="checkbox" for="'.$id.'">';
            $html .= '<input type="hidden" name="'.$this->name.'"'.' value="0"/>'."\n";
            $html .= '<input'.$this->getHtmlAttr($this->items()).' '.$this->_getData().'value="1"/>'.$this->htmlLabelSmall()."\n";
            //$html .= $this->htmlLabel();
            $html .= $this->htmlDescription();
            $html .= $this->htmlWrapperEnd();
        }

        return $html;
    }

    private function _getData()
    {
        $html = '';
        if (is_array($this->data)) {
            foreach ($this->data as $key => $val) {
                $html .= 'data-'.$key.'="';
                if (is_array($val)) {
                    $html .= implode(', ', $val);
                } else {
                    $html .= $val;
                }
                $html .= '" ';
            }
        }

        return $html;
    }
}
