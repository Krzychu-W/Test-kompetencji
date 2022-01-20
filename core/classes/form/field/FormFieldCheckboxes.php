<?php

class FormFieldCheckboxes extends qFormField
{
    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_CHECKBOXES, $fieldAttr);
        $this->setHtmlAttrib();
        $this->select = array();
        $this->classess = array();
        $this->checked = array();
        $this->readonly = array();
        $this->iTitle = array();
        $this->onchange = '';
        $this->data_id = false;
    }

    public function html():string
    {
        $html = $this->htmlWrapper();
        $html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= "<div class=\"{$this->item('#class')}-checkboxes\">";
        $id = $this->item('id');
        $name = $this->item('name');
        foreach ($this->item('select') as $key => $label) {
            $checked = '';
            if ((is_array($this->value) && isset($this->value[$key])) || in_array($key, $this->checked)) {
                $checked = ' checked="checked"';
            }
            $disabled = '';
            if (in_array($key, $this->readonly)) {
                $disabled = ' disabled="disabled"';
            }
            $title = '';
            if (isset($this->iTitle[$key])) {
                $title = ' title="'.htmlspecialchars($this->iTitle[$key]).'"';
            }
            if (!empty($this->onchange)) {
                $onchange = ' onchange="'.htmlspecialchars($this->onchange).'"';
            } else {
                $onchange = '';
            }
            $data_id = '';
            if (false !== $this->data_id) {
                $data_id = ' data-id="'.$key.'"';
            }
            $html .= "<div class=\"{$this->item('#class')}-checkboxes-item".(isset($this->item('classess')[$key]) ? ' '.$this->item('classess')[$key] : '').'">';
            $html .= "<input type=\"checkbox\" id=\"{$id}-{$key}\" name=\"{$name}[{$key}]\" value=1{$checked}{$disabled}{$title}{$onchange}{$data_id} />\n";
            $html .= '<label for="'.$id.'-'.$key.'" class="checkboxs-item"'.$title.'>'.$label.'</label>'."\n";
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= $this->htmlContentEnd();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }
}
