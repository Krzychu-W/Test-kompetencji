<?php

class FormFieldGroup extends qFormField
{
    public $tagAttrib = array();

    public function __construct($form) {
        parent::__construct(qFormField::TYPE_GROUP, $form);
        $this->setHtmlAttrib('id', 'class');
        $this->setItem('collapsible', -1);
    }

    public function html(): string
    {
        $html = '<div';
        $html .= $this->getHtmlAttr($this->items());
        $html .= ">\n";
        $title = '';
        if ($this->hasItem('title')) {
            $title = $this->item('title');
        }
        else if ($this->hasItem('label')) {
            $title = $this->item('label');
        }
        $collapsible = $this->getItem('collapsible', -1);

        if ($collapsible == -1) {
            $display1 = 'hide';
            $display2 = 'hide';
            $display3 = 'show';
        }
        else if ($collapsible == 0) {
            $display1 = 'show';
            $display2 = 'hide';
            $display3 = 'hide';
        }
        else if ($collapsible == 1) {
            $display1 = 'hide';
            $display2 = 'show';
            $display3 = 'show';
        }
        if ($collapsible >= 0) {
            $collap  = '<span class="develop '.$display1.' click">Rozwiń</span>';
            $collap .= '<span class="collapse '.$display2.' click">Ukryj</span>';
        }
        else {
            $collap = '';
        }
        if ($title || $collap) {
            $html .= '<div class="legend">'.$title.$collap.'</div>'."\n";
        }
        $html .= '<div class="collapsible '.$display3.'">';

        $html .= $this->htmlDescription();
        foreach ($this->fields()->items() as $item) {
            if ($item->display()) {
                $html .= $item->html();
            }
        }
        $html .= "</div></div>\n";

        return $html;
    }
}
