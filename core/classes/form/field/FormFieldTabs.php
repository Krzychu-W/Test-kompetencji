<?php

class FormFieldTabs extends qFormField
{
    public $tagAttrib = array('class');

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_TABS, $fieldAttr);
        $this->setHtmlAttrib('class');
    }

    public function html(): string
    {
        $html = $this->htmlWrapper();
        //$html .= $this->htmlLabel();
        $htmlNag = '';
        $lp = 1;
        $field_pos = $this->item('position');
        if ('left' == $field_pos or 0 != strlen($field_pos)) {
            $pos = ' tabs-left';
        } else {
            $pos = '';
        }
        $html .= '<div class="tabbable'.$pos."\">\n";
        foreach ($this->fields()->getItems() as $item) {
            if (1 == $lp) {
                $active = 'class="active" ';
            } else {
                $active = '';
            }

            if ($item->item('onclick')) {
                $onclick = 'onclick="'.$item->item('onclick').'"';
            } else {
                $onclick = '';
            }

            if (qFormField::TYPE_TAB == $item->type) {
                $htmlNag .= '  <li '.$active.'><a href="#tab-element-'.$item->item('field').'" '.$onclick.'>'.$item->item('label').'</a></li>'."\n";
            }
            ++$lp;
        }
        if ('' != $htmlNag) {
            $html .= '<ul class="nav nav-tabs" id="tabs-'.$this->item('field').'">'."\n";
            $html .= $htmlNag;
            $html .= "</ul>\n";
        }
        if (!$this->item('nobody', false)) {
            $html .= '<div class="tab-content">'."\n";
            $lp = 1;
            foreach ($this->fields()->getItems() as $item) {
                if (1 == $lp) {
                    $active = ' active';
                } else {
                    $active = '';
                }
                $html .= '<div class="tab-pane'.$active.'" id="tab-element-'.$item->item('field').'">';
                $html .= $item->html();
                $html .= '</div>';
                ++$lp;
            }
            $html .= "</div>\n";  // koniec bodu
        }
        $html .= "</div>\n";  // koniec tabsów
        $html .= $this->htmlWrapperEnd();
        $js = "$('#tabs-".$this->item('field')." a').click(function (e) {e.preventDefault(); $(this).tab('show'); })";
        $html .= "<script>{$js}</script>\n";
        //Ajax::addColorboxEval($js);

        return $html;
    }
}
