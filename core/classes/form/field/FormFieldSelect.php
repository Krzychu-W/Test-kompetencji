<?php

class FormFieldSelect extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_SELECT, $formAttr);
        $this->setHtmlAttrib('id', 'class', 'onChange');
        //$this->setItem('span', '2');
        $this->setItem('slider', false);
        $this->setItem('disabled', false);
        $this->setItem('onchange', false);
        $this->setItem('multiple', false);
        $this->setItem('data', 3);
    }

    public function _html(): string
    {
        $opt = $this->items();
        $opt['class'] .= ' span'.$this->span;
        if ($this->slider) {
            $html = '<div id="'.$this->id.'-number">'.$this->value.'</div>';
        } else {
            $html = '';
        }
        $html .= '<div class="field-line">';
        if ($this->multiple) {
            $mName = '[]';
        } else {
            $mName = '';
        }
        $html .= '<select id="'.$this->id.'" class="'.$opt['class'].'" name="'.$this->name.$mName.'" ';
        if ($this->onchange) {
            $html .= ' onchange="'.$this->onchange.'"';
        } elseif ($this->onChange) {
            $html .= ' onChange="'.$this->onChange.'"';
        }
        if ($this->disabled) {
            $html .= ' disabled="disabled"';
        }
        if ($this->multiple) {
            $html .= ' multiple="multiple"';
        }
        if ($this->size) {
            $html .= ' size="'.$this->size.'"';
        }
        $html .= ">\n";
        $countOptions = count($this->item('options', array()));
        if (!is_array($this->value)) {
            $this->value = array($this->value);
        }
        foreach ($this->item('options', array()) as $val => $opis) {
            if (in_array($val, $this->value)) {
                if ($this->classes) {
                    $html .= '<option class="'.$this->classes[$val]
                  .'" value="'.$val.'" '.$this->_getData($val)
                  .' selected="selected">'.$opis."</option>\n";
                } else {
                    $html .= '<option value="'.$val.'" '.$this->_getData($val)
                  .' selected="selected">'.$opis."</option>\n";
                }
            } else {
                if ($this->classes) {
                    $html .= '<option class="'.$this->classes[$val]
                  .'" value="'.$val.'" '.$this->_getData($val)
                  .' >'.$opis."</option>\n";
                } else {
                    $html .= '<option value="'.$val.'" '.$this->_getData($val)
                  .' >'.$opis."</option>\n";
                }
            }
        }
        $html .= "</select>\n";
        $html .= "</div>\n";

        if ($this->slider) {
            $script = "$(function() {
                    var select = $( '#".$this->id."' );
                    var slider = $( '<div id=\'form-field-slider\' class=\"span6\"></div><br/>' ).insertAfter( select ).slider({
                      min: 1,
                      max: ".$countOptions.",
                      range: 'min',
                      value: select[ 0 ].selectedIndex + 1,
                      slide: function( event, ui ) {
                        select[ 0 ].selectedIndex = ui.value - 1;
                        $('#".$this->id."-number').text(ui.value - 1);
                        //$('#".$this->id."-val').innerHtml(ui.value);
                      }
                    });
                    $('<span id=\"".$this->id."-val\"></span>').insertAfter( slider );
                    $(select).hide();
                    //$('#".$this->id."-number').text('".$val."');
                  });";
            //Ajax::addColorboxEval($script);
        }

        return $html;
    }

    private function _getData($key)
    {
        $html = '';
        if (is_array($this->data)) {
            $html .= 'data-'.$this->data['name'].'="';
            if (is_array($this->data['value'][$key])) {
                $html .= implode(', ', $this->data['value'][$key]);
            } else {
                $html .= $this->data['value'][$key];
            }
            $html .= '"';
        }

        return $html;
    }
}
