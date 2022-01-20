<?php

class FormFieldRating extends qFormField
{
    public $tagAttrib = array();

    public function __construct($fieldAttr)
    {
        parent::__construct(qFormField::TYPE_TEXT, $fieldAttr);
        $this->setHtmlAttrib('id', 'class', 'name', 'value', 'type', 'readonly');
        $this->setItem('size', '25');
        $this->setItem('maxlength', '255');
        $this->setItem('span', '2');
        $this->setItem('col_class', false);
    }

    public function _html(): string
    {
        $opt = $this->items();
        if (false !== $this->span) {
            $opt['class'] .= ' span'.$this->span;
        }
        $html = '<div class="field-line">';
        $html .= '<div id="star-rating" class="rating">';
        for ($i = 0; $i < 5; ++$i) {
            $html .= '<span onclick="reviews(this);" onmouseover="reviewsHover(this);" onmouseout="reviewsHoverOut(this);" class="star star-left" data-starval="'.($i + 0.5).'"></span>';
            $html .= '<span onclick="reviews(this);" onmouseover="reviewsHover(this);" onmouseout="reviewsHoverOut(this);" class="star star-right" data-starval="'.($i + 1).'"></span>';
        }
        $html .= '</div>';
        $html .= $this->in_line_prefix.'<input ';
        $html .= $this->getHtmlAttr($opt);
        $html .= " />{$this->in_line_suffix}\n";
        $html .= "</div>\n";

        return $html;
    }
}
