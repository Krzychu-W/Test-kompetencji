<?php

/**
 * Class qFormField
 *
 * @property $error Komunikat o błędzie
 * @property $id Id pola
 * @property $class Klasa pola
 * @property $disabled
 * @property $in_line_prefix
 * @property $in_line_suffix
 * @property $value
 */
class qFormField extends qFormTree
{
    const TYPE_FORM = 'form';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SWITCHER = 'switcher';
    const TYPE_CHECKBOXES = 'checkboxes';
    const TYPE_RADIOS = 'radios';
    const TYPE_RADIO = 'radio';
    const TYPE_BUTTON = 'button';
    const TYPE_SELECT = 'select';
    const TYPE_INTEGER = 'integer';
    const TYPE_RANGE = 'range';
    const TYPE_HTML = 'html';
    const TYPE_SUBMIT = 'submit';
    const TYPE_CONFIRM = 'confirm';
    const TYPE_ALTER = 'alter';
    const TYPE_WRAPPER = 'wrapper';
    const TYPE_TAB = 'tab';
    const TYPE_TABS = 'tabs';
    const TYPE_GROUP = 'group';
    const TYPE_ALIAS = 'alias';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_PASS = 'password';
    const TYPE_PASS_CONFIRM = 'password_confirm';
    const TYPE_INVOICE = 'invoice';
    const TYPE_TPL = 'tpl';
    const TYPE_NONE = 'none';
    const TYPE_FIELD_LIST = 'field_list';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'tel';
    const TYPE_WWW = 'url';
    const TYPE_VALUE = 'value';
    const TYPE_EXTERNAL = 'external';
    const TYPE_SCRIPT = 'script';

    const DATE_FORMAT_DATE = 1;
    const DATE_FORMAT_DATE_TIME = 2;

    // lista atrybutów zwracanych poleceniem getAttrib
    private $htmlAttr = array();
    private $form;
    private $display = true;
    private $additionalWrapperClass = array();

    protected $dateFormat = false;

    public function __construct($type, &$form)
    {
        parent::__construct($form);
        // ustawiena domyślne dla wszystkich
        $this->setItem('type', $type);
        $this->form = $form;
        $formAttr = $form->attribs();
        if ($formAttr) {
            $this->setItem('#id', $formAttr->item('id'));
            $this->setItem('#class', $formAttr->item('class'));
            $this->setItem('#name', $formAttr->item('name'));
        }
        $this->setItem('error', '');
        $this->setItem('errors', array());
        $this->setItem('value', '');
        $this->setItem('placeholder', '');
        $this->setItem('prefix', '');
        $this->setItem('suffix', '');
        $this->setItem('field_prefix', '');
        $this->setItem('field_suffix', '');
        $this->setItem('in_line_prefix', '');
        $this->setItem('in_line_suffix', '');
        $this->setItem('tag', '');
        $this->setItem('field', '');
        $this->setItem('appear', true);
        $this->setItem('span', 3);
        $this->setItem('spanWrapper', false);
        $this->setItem('emptyError', false);
        $this->setHtmlAttrib('data');
    }

    public function form()
    {
        return $this->form;
    }

    public function init($attr)
    {
        if (is_string($attr)) {
            $attr = new qAttr($attr);
        }
        foreach ($attr->getItems() as $key => $item) {
            $this->setItem($key, $item);
        }
    }

    public function display($method = false)
    {
        if (false === $method) {
            if (is_array($this->display)) {
                $methodName = $this->display[0];

                return $this->form()->$methodName();
            } else {
                return $this->display;
            }
        } else {
            $this->display = array($method);
        }
    }

    public function setErrors($key, $value)
    {
        $error = parent::item('errors');
        $error[$key] = $value;
        parent::setItem('errors', $error);
    }

    public function setItem($key, $value)
    {
        if ('name' == $key) {
            $newValue = '';
            foreach (explode('.', $value) as $item) {
                $newValue .= '['.$item.']';
            }
            parent::setItem('class', $this->item('#class').'-field-'.$this->item('type'));
            parent::setItem('id', $this->item('#id').'-field-'.str_replace('.', '-', $value));
            parent::setItem('field', $value);
            $value = $this->item('#name').$newValue;
        }
        parent::setItem($key, $value);
    }

    public function ifAttr($name)
    {
        return false;
    }

    public function getHtmlAttr($attribs, $addName = false, $setValue = false)
    {
        $result = '';
        foreach ($attribs as $key => $value) {
            if (in_array($key, $this->htmlAttr)) {
                if ($addName && 'name' == $key) {
                    $value .= $addName;
                }
                if ($setValue !== false && 'value' == $key) {
                    $value = $setValue;
                }
                //$result .= ' '.$key.'="'.self::quot($value).'"';
                if (is_object($value)) {
                    
                } elseif (is_array($value)) {
                    
                } elseif (strlen($value) > 0) {
                    $result .= ' '.$key.'="'.$value.'"';
                }
            }
        }
        
        return $result;
    }

    public function setHtmlAttrib()
    {
        $keys = func_get_args();
        foreach ($keys as $key) {
            if (!in_array($key, $this->htmlAttr)) {
                $this->htmlAttr[] = $key;
            }
        }
    }

    public function htmlAttrib($key = false)
    {
        if (!$key) {
            return $this->htmlAttr;
        }

        return in_array($key, $this->htmlAttr);
    }

    /**
     * Generuje html pola
     *
     * @return string
     */
    public function html(): string
    {
        $html = $this->htmlWrapper();
        $html .= $this->htmlLabel();
        $html .= $this->htmlContent();
        $html .= $this->_html();
        $html .= $this->htmlContentEnd();
        $html .= $this->htmlDescription();
        $html .= $this->htmlWrapperEnd();

        return $html;
    }

    public function htmlWrapper($extraClass = false)
    {
        $style = '';
        if (!$this->item('appear', true)) {
            $style = ' style="display:none"';
        }
        $class = $this->getAllWrapperClass($this->item('class').'-wrapper');
        if ($extraClass) {
            $class .= ' '.$extraClass;
        }
        $spanWrapper = $this->item('spanWrapper', false);
        if ($spanWrapper) {
            $class .= ' span'.$spanWrapper;
        }

        return $this->item('prefix')."<div id=\"{$this->id}-wrapper\" class=\"$class\"{$style}>\n";
    }

    public function htmlWrapperEnd()
    {
        $result = '';
        $error = $this->item('error', '');
        if ('' != $error || $this->form->attr('emptyError', false) || $this->item('emptyError', false)) {
            $result .= "<p class=\"error\">{$error}</p>\n";
        }
        $result .= '</div>'.$this->item('suffix')."\n";

        return $result;
    }

    public function wrapperError()
    {
        $result = '';
        $error = $this->item('error', '');
        if ('' != $error || $this->form->attr('emptyError', false) || $this->item('emptyError', false)) {
            $result .= "<p class=\"error\">{$error}</p>\n";
        }

        return $result;
    }

    public function htmlLabel($title = false)
    {
        if (!$title) {
            $title = $this->item('title', $this->item('label', ''));
        }
        $html = '';
        if ($title) {
            if ('`' == substr($title, 0, 1)) {
                $title = qTrans::get(substr(substr($title, 0, strlen($title) - 1), 1));
            }
            $html .= "<label for=\"{$this->id}\">{$title}";
            $html .= $this->requiredLabel();
            $html .= "</label>\n";
        }

        return $html;
    }

    public function requiredLabel()
    {
        $html = '';
        if ($this->hasItem('required') && $this->item('required')) {
            $star = '*';
            $text = qTrans::get('field-required');
            $required = $this->item('required');
            if (false !== $required) {
                if (is_string($required)) {
                    $tab = explode(':', $required);
                    if (2 == count($tab)) {
                        $star = $tab[0];
                        $text = $tab[1];
                    } else {
                        $text = $required;
                    }
                }
                $html .= " <span class=\"{$this->item('#class')}-label-required\" title=\"{$text}\">{$star}</span>";
            }
        }

        return $html;
    }

    public function htmlLabelSmall($title = false)
    {
        if (!$title) {
            $title = $this->item('title', $this->item('label', ''));
        }
        if ($title) {
            $html = "<label for=\"{$this->id}\">{$title}";
            $html .= $this->requiredLabel();
            $html .= "</label>\n";
        }

        return $html;
    }

    public function htmlContent($addClass = [])
    {
        $res = '<div class="'.$this->class.'-content '.$this->item('#class').'-field-content '.implode(' ', $addClass).'">';
        $res .= $this->item('field_prefix');

        return $res;
    }

    public function htmlContentEnd()
    {
        $res = $this->item('field_suffix');
        $res .= '</div>';

        return $res;
    }

    public function htmlDescription()
    {
        $html = '';
        if ($this->hasItem('description')) {
            $desc = $this->item('description');
            if ('' != $desc) {
                $html .= '<div id="'.$this->id.'-description" class="'.$this->item('#class').'-description">'.$desc.'</div>'."\n";
            }
        }

        return $html;
    }

    public function addOption($key, $value)
    {
        if (!$this->hasItem('option')) {
            $option = new qItems();
            $option->setItem($key, $value);
            $this->setItem('option', $option);
        } else {
            $this->item('option')->setItem($key, $value);
        }
    }

    public function addOptions($array)
    {
        if (!$this->hasItem('option')) {
            $option = new qItems();
        } else {
            $option = $this->item('option');
        }

        foreach ($array as $key => $value) {
            $option->setItem($key, $value);
        }
    }

    public function isValue()
    {
        // czy to pole zwraca wartość
        return true;
    }

    public function isTree()
    {
        // czy to pole zwraca wartość
        return false;
    }

    public function fileds()
    {
        $result = array();
        foreach ($this->getItems() as $key => $item) {
            if (is_object($item)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    public function addClass($name)
    {
        $this->additionalWrapperClass[] = $name;
    }

    public function getAllWrapperClass($primaryClass)
    {
        $result = $primaryClass;
        foreach ($this->additionalWrapperClass as $class) {
            $result .= ' '.$class;
        }

        return $result;
    }

    public static function quot($value)
    {
        return str_replace('"', '&quot;', $value);
    }

    public function viewHtml($content = false)
    {
        $class = $this->class.'-view-wrapper';
        $html = "<div id=\"{$this->id}-view-wrapper\" class=\"$class\">\n";
        $title = $this->item('title', $this->item('label', ''));
        $html .= "<label for=\"{$this->id}-viewLabel\">{$title}</label>\n";
        $html .= '<div class="'.$this->class.'-view-content '.$this->item('#class').'-field-view-content">';
        if ($content) {
            $html .= $content;
        } else {
            $html .= $this->value;
        }
        $html .= '</div>';
        $html .= '</div>'."\n";

        return $html;
    }

    public function translate($noKey = array())
    {
        /*
        $noKey = array_merge(array('class', 'id', 'name'), $noKey);
        foreach ($this->items() as $key => $item) {
            if (is_string($item) && !in_array($key, $noKey)) {
                $this->setItem($key, TransString::compile($item));
            }
        }
        */
    }

    public function dateFormat($set = null)
    {
        if (null === $set) {
            return $this->dateFormat;
        }
        $this->dateFormat = $set;
    }

    public function fromParent()
    {
        $html = '';
        if ($this->form->node()->hasParent()) {
            $parentFields = $this->form->node()->getParentFields();
            $fieldName = $this->item('field');
            if (array_key_exists($fieldName, $parentFields) && !$this->readonly) {
                if ($parentFields[$fieldName]) {
                    $sClass = 'extend-parent-off';
                    $value = 1;
                } else {
                    $sClass = 'extend-parent-on';
                    $value = 0;
                }
                $html .= '<div class="form-field-from-parent '.$sClass.'" onclick="qAnt.module.node.edit.extendParent(this)">';
                //$html .= 'xXx';
                $html .= '<input type="hidden" name="extend_parent['.$fieldName.']" value="'.$value.'" />';
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function parseDataAttr()
    {
        $data = $this->item('data');
        $attribs = '';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $attribs .= 'data-'.$key.'="'.$value.'" ';
            }
        }

        return $attribs;
    }

    public function _html(): string
    {
        return '';
    }
}
