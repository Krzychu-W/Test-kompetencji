<?php

/*
 *  Obsługa przycisków typu radio zebranych w :
 *  $this->html='tableHorizontal'  - tabelę o nagłówkach $this->headers rozciągniętych w poziomie
 *  $this->html='horizontal'   - lista rozciągnięta w poziomie ( radia we wspólnym div )
 *  $this->html='vertical'   - lista radii w pionie z ewentualnymi labelami z $this->headers ( każde ma div )
 */

class FormFieldRadio extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_RADIO, $formAttr);
        $this->setHtmlAttrib('html', 'onChange', 'value');     // Maro
    }

    public function html(): string
    {
        if ($this->asWrapper) {
            $html = '<td>';
        } else {
            $html = $this->htmlWrapper();
            $html .= $this->htmlLabel();
        }
        if ($this->checked) {
            $checked = 'checked="$checked"';
        } else {
            $checked = '';
        }
        $html .= '<td><input type="radio"'.$this->getHtmlAttr($this->items())." name=\"{$this->radio_name}\" {$checked}".' onChange='.$this->onChange.">{$opis}</input></td>\n";
        if ($this->asWrapper) {
            $html .= '<p>'.$this->htmlLabelSmall().'</p>';
            $html .= '</td>';
        } else {
            $html .= $this->htmlDescription();
            $html .= $this->htmlWrapperEnd();
        }

        return $html;
    }
}
