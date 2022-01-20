<?php

/*
 *  Obsługa przycisków typu radio zebranych w :
 *  $this->html='tableHorizontal'  - tabelę o nagłówkach $this->headers rozciągniętych w poziomie
 *  $this->html='horizontal'   - lista rozciągnięta w poziomie ( radia we wspólnym div )
 *  $this->html='vertical'   - lista radii w pionie z ewentualnymi labelami z $this->headers ( każde ma div )
 */

class FormFieldRadios extends qFormField
{
    public $tagAttrib = array('onChange');

    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_RADIOS, $formAttr);
        $this->setHtmlAttrib('name', 'checked', 'html', 'onChange', 'disabled');
    }

    public function html(): string
    {
        $html = $this->htmlWrapper();
        $html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= $this->_html();
        $html .= $this->htmlContentEnd();
        //$html .= $this->htmlDescription();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function _html(): string
    {
        $opt = $this->items();
        if (false !== $this->span) {
            $opt['class'] .= ' span'.$this->span;
        }
        $html = '<div class="field-line">';
        if ('tableHorizontal' == $this->html) {
            $ih = 0;
            $ic = 0;
            $html .= "<div id=\"{$this->id}\"class=\"{$this->class}\">\n";
            $html .= $caption ? "<table><caption>$caption</caption><thead><tr>" : '<table><thead><tr>';
            foreach ($headers as $num => $head) {
                $html .= "<th> $head </th>";
                ++$ih;
            }
            $html .= '</tr></thead><tbody>';
            foreach ($this->item('select', array()) as $val => $opis) {
                $checked = '';
                if (0 == $ic % $ih) {
                    $html .= '<tr>';
                }
                if (strlen($this->value) > 0 && $val == $this->value) {
                    $checked = 'checked';
                }
                $onchange = '';
                if ('' != $this->onChange && '/' != $this->onChange) {
                    $onchange = ' onchange="'.$this->onChange.'"';
                }
                $html .= '<td><input type="radio"'.$this->getHtmlAttr($this->items()).' value='.$val." {$checked}".$onchangee.">{$opis}</input></td>\n";
                ++$ic;
                if (0 == $ic % $ih) {
                    $html .= '</tr>';
                }
            }
            $html .= "</tbody></table></div>\n";
        } else {
            $html .= "<div id=\"{$this->id}\" class=\"{$this->class}\">\n";
            $licz = 0;
            $sel = $this->item('select', array());
            $classes = $this->item('classes', array());

            foreach ($sel as $val => $opis) {
                $additionalClass = '';
                if (count($classes) > 0) {
                    $additionalClass = 'class="'.$classes[$val].'"';
                }
                ++$licz;
                $checked = '';
                if (strlen($this->value) > 0 && $val == $this->value) {
                    $checked = 'checked';        //   Macho
                }
                $onchange = '';
                if ('' != $this->onChange && '/' != $this->onChange) {
                    $onchange = ' onchange="'.$this->onChange.'"';
                }
                if (stristr($this->html, 'horizontal')) {
                    $html .= "<input {$additionalClass} id=\"{$this->id}-{$licz}\" type=\"radio\"".$this->getHtmlAttr($this->items()).' value='.$val." {$checked}".$onchange.' '.($this->disabled ? 'disabled="disabled"' : '')." />\n";
                    $html .= '<label for="'.$this->id.'-'.$licz.'" class="radios-item">'.$opis.'</label>'."\n";
                } else {
                    $html .= "<div class=\"{$this->item('#class')}-radio-item\">";
                    $html .= "<input {$additionalClass} id=\"{$this->id}-{$licz}\" type=\"radio\"".$this->getHtmlAttr($this->items()).' value='.$val." {$checked}".$onchange.' '.($this->disabled ? 'disabled="disabled"' : '')." />\n";
                    $html .= '<label for="'.$this->id.'-'.$licz.'" class="radios-item">'.$opis.'</label>'."\n";
                    $html .= '</div>';
                }
            }
            $html .= "</div>\n";
            $html .= $this->htmlDescription();
        }
        $html .= "</div>\n";

        return $html;
    }
}
