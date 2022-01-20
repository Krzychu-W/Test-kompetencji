<?php

class FormFieldSwitcher extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_SWITCHER, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'checked', 'size', 'onchange');
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
            $html .= '<input type="checkbox"'.$this->getHtmlAttr($this->items()).' value="1"/>'.$this->htmlLabelSmall()."\n";
            $html .= '<label class="onoffswitch-label" for="'.$id.'">';
            $html .= '<div class="onoffswitch-inner"></div>';
            $html .= '<div class="onoffswitch-switch"></div>';
            $html .= '</label>';
            $html .= '</td>';
        } else {
            $id = $this->id;
            $html .= '<div class="form-field-switcher"><input type="checkbox"'.$this->getHtmlAttr($this->items()).' value="1"/>';

            $html .= '<label class="onoffswitch-label" for="'.$id.'">';
            $html .= '<div class="onoffswitch-inner"></div>';
            $html .= '<div class="onoffswitch-switch"></div>';
            $html .= '</label>'.$this->htmlLabelSmall().'</div>';

            $html .= $this->htmlDescription();
            $html .= $this->htmlWrapperEnd();
        }

        return $html;
    }
}
