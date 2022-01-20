<?php

class FormFieldPassword extends qFormField
{
    public $tagAttrib = array('onchange', 'class', 'size', 'maxlength');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_PASSWORD, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'size', 'maxlength', 'value', 'type', 'readonly', 'placeholder', 'autocomplete', 'powerpass');
        $this->setItem('span', '2');
        $this->setItem('prefix', '');
    }

    public function html(): string
    {
        $html = '';
        $html .= $this->htmlWrapper();
        $html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= $this->_html();
        $html .= $this->htmlContentEnd();

        
        qLayout::addScript(file_get_contents(dirname(__DIR__).DS.'javascript'.DS.'setpass.js'));
        
        //Layout::jsModule('customer', 'setpass.js');
        $html .= '<div class=password-power-text style=visibility: hidden>'.qTrans::get('form.password-confirm-power').':</div><div class=password-power-content data-match="'.qTrans::get('form.password-matched').'" data-nomatch="'.qTrans::get('form.password-notmatched').'"><div class="power"></div></div>';
        $html .= '<ul class=password-power-info style=visibility: hidden>'.qTrans::get('form.password-power-info').':';
        $html .= '<li class=password-power-1>'.qTrans::get('form.password-power-1').'</li>';
        $html .= '<li class=password-power-2>'.qTrans::get('form.password-power-2').'</li>';
        $html .= '<li class=password-power-3>'.qTrans::get('form.password-power-3').'</li>';
        $html .= '<li class=password-power-4>'.qTrans::get('form.password-power-4').'</li>';
        $html .= '<li class=password-power-5>'.qTrans::get('form.password-power-5').'</li>';
        $html .= '</ul>';
        
        $this->setItem('powerpass', null);
        $html .= $this->htmlDescription();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function _html(): string
    {
        $opt = $this->getItems();
        if (false !== $this->span) {
            $opt['class'] .= ' span'.$this->span;
        }
        $html = ''; //$this->field_prefix;
        $html .= '<div class="field-line">';
        $html .= '<input ';
        $html .= $this->getHtmlAttr($opt);
        $html .= " />\n";
        $html .= "</div>\n";

        return $html;
    }
}
