<?php

/**
 * Class FormFieldTextarea
 *
 * @property $cols Ilość kolumn (domyślnie 50)
 * @property $rows Ilość wierszy (domyślnie 5)
 * @property $toolbar 0|1 - czy ma pojawiaś się pasek narzędzi
 */
class FormFieldTextarea extends qFormField
{
    /**
     * FormFieldTextarea constructor.
     * @param $formAttr
     */
    public function __construct($formAttr)
    {
        parent::__construct(qFormField::TYPE_TEXTAREA, $formAttr);
        // ustawienia domyślne
        $this->setHtmlAttrib('id', 'class', 'name', 'cols', 'rows', 'onclick', 'placeholder', 'style');
        $this->setItem('cols', '50');
        $this->setItem('rows', '5');
        $this->setItem('toolbar', 0);
        $this->setItem('ckeditor', 0);
    }

    public function _html(): string
    {
        $opt = $this->getItems();

        if ($this->toolbar == 1) {
            $opt['class'] .= ' toolbar';
        }
        if ($this->ckeditor == 1) {
            $opt['class'] .= ' ckeditor';
            qCkeditor::add();
            //qCkeditor::addSelector('#form-field-body');
        }
        $html = '';
        $html .= '<div class="field-line">';
        if ($this->toolbar) {
            $html .= '<div class="button-line">';
            $html .= '<button class="full-screen">full-screen</button>';
            $html .= '<p>';
            $html .= '</p>';
            $html .= '<p>';
            $html .= '<button class="bold"><strong>B</strong></button>';
            $html .= '<button class="italic"><em>I</em></button>';
            $html .= '<button class="del"><del>S</del></button>';
            $html .= '<button class="sup">X<sup>y</sup></button>';
            $html .= '<button class="sub">X<sub>a</sub></button>';
            $html .= '<button class="ins"><ins>U</ins></button>';
            $html .= '<button class="html">html</button>';
            $html .= '&nbsp;';
            $html .= '<button class="h1">H1</button>';
            $html .= '<button class="h2">H2</button>';
            $html .= '<button class="h3">H3</button>';
            $html .= '<button class="h4">H4</button>';
            $html .= '<button class="h5">H5</button>';
            $html .= '<button class="h6">H6</button>';
            $html .= '</p>';

            $html .= '<p>';
            $html .= '<button class="div">{div}</button>';
            $html .= '<button class="precode">{pre}{code}</em></button>';
            $html .= '<button class="html">{html}</em></button>';
            $html .= '<button class="tags">{tag}</button>';
            $html .= '&nbsp;';
            $html .= '<button class="url">url</button>';
            $html .= '<button class="img">img</button>';
            $html .= '<button class="ref">ref<sup>*</sup></button>';
            $html .= '<button class="ref1">ref<sup>1</sup></button>';



            $html .= '</p>';
            $html .= '</div>';
        }
        $html .= '<textarea';
        $html .= $this->getHtmlAttr($opt);
        $html .= '>'.$this->value."</textarea>\n";
        $html .= "</div>\n";

        return $html;
    }
}
