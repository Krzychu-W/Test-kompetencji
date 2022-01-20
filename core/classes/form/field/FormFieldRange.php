<?php

class FormFieldRange extends qFormField
{
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_RANGE, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'type', 'step', 'min', 'max', 'labelFrom');

        $this->setItem('step', '1');
        //$this->setItem('min', '0');
        //$this->setItem('value', ['from' => '', 'to' => '']);
        $this->setItem('ranges', []);
    }

    public function _html(): string
    {
        $opt = $this->items();
        $opt['type'] = 'number';

        $optFrom = $opt;
        $optFrom['id'] .= '-from';

        $optTo = $opt;
        $optTo['id'] .= '-to';
        if (is_array($this->value) && isset($this->value['from'])) {
            $from = $this->value['from'] === '' ? '' : intval($this->value['from']);
        } else {
            $from = '';
        }
        if (is_array($this->value) && isset($this->value['to'])) {
            $to = $this->value['to'] === '' ? '' : intval($this->value['to']);
        } else {
            $to = '';
        }
        $html = '';
        $html .= '<div class="range-from">';
        if (!empty($this->labelFrom)) {
            $html .= '<label for="'.$this->id.'-from">'.$this->labelFrom.'</label>';
        }
        $html .= '<input '.$this->getHtmlAttr($optFrom, '[from]', $from).' />';
        $html .= '</div>';

        $html .= '<div class="range-to">';
        if (!empty($this->labelTo)) {
            $html .= '<label for="'.$this->id.'-to">'.$this->labelTo.'</label>';
        }
        $html .= '<input '.$this->getHtmlAttr($optTo, '[to]', $to).' />';
        $html .= '</div>';

        // Ranges
        if (!empty($this->getItems()['ranges'])) {
            $html .= '<div class="ranges"><ul class="range-list">';

            foreach ($this->getItems()['ranges'] as $range) {
                $html .= '<li class="range-item"><span class="from">'.$range[0].'</span> - <span class="to">'.$range[1].'</span></li>';
            }

            $html .= '</ul></div>';
        }

        return $html;
    }
}
