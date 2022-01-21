<?php

/*
 * Atrybuty zarówno formy jak i pól są dwojga rodzaju zwykłe i sterujące, które zaczynają się od #
 */

class qForm extends qFormTree
{
    private $attr;
    protected $refFields = array();
    private $comment = '';

    protected $token = true;

    //private $hash_token = '';

    public function __construct($params = [])
    {
        parent::__construct($this);
        // ustanowienie domyślnych atrybutów formy
        $this->attr = new qAttr('id:form;class:form;name:form;method:post;accept-charset:UTF-8');
        $this->init($params);
    }

    public function init()
    {
    }

    public function attribs()
    {
        return $this->attr;
    }

    public function attr($key, $dafault)
    {
        return $this->attr->item($key, $dafault);
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function __get($key)
    {
        return $this->field($key);
    }

    /**
     * metoda ustawia wartości "value" dla pól.
     *
     * @param array $items - tablica z wartościami
     */
    public function setValues($params)
    {
        //$values = array();
        $items = $this->tree2item($params);
        foreach ($this->refFields as $key => $field) {
            if ($field->isValue()) {
                if (isset($items[$key])) {
                    $field->value = $items[$key];
                }
            }
        }
    }

    public function setValue($key, $value)
    {
        if (isset($this->refFields[$key])) {
            $this->refFields[$key]->value = $value;
        }
    }

    public function field($key)
    {
        if (isset($this->refFields[$key])) {
            return $this->refFields[$key];
        }

        return false;
    }

    public function delField($key)
    {
        $this->_delField($this->fields(), $key);
    }

    protected function _delField($items, $field)
    {
        foreach ($items as $key => $item) {
            if ($key == $field) {
                $items->delItem($key);
            } else {
                $this->_delField($item->fields(), $field);
            }
        }
    }

    /**
     * metoda zwraca wartości "value" dla pól
     * return array;.
     */
    public function values()
    {
        $values = array();
        foreach ($this->refFields as $key => $field) {
            if ($field->isValue()) {
                $values[$key] = $field->value;
            }
        }
        $result = $this->item2tree($values);

        return $result;
    }

    public function keys()
    {
        $res = array();
        foreach ($this->refFields as $key => $field) {
            $res[] = $key;
        }

        return $res;
    }

    public function value($key, $default = '') {
        if (isset($this->refFields[$key])) {
            $field = $this->refFields[$key];
            if ($field->isValue()) {
                return $field->value;
            }
        }
        return $default;
    }

    public function fieldsAsArray()
    {
        $fields = array();
        foreach ($this->items() as $key => $field) {
            $field->html();
            $fields[$key] = $field->items();
        }
        $attr = $this->attr->items();

        return array('attr' => $attr, 'fields' => $result);
    }

    public function fieldsName()
    {
        return $this->keys();
    }

    public function getForm($tpl = 'form')
    {
        $block = new qTemplate();
        $block->form = $this;

        return $block->render('form/general');
    }

    public function setFormToken($_name, $_id)
    {
        // nowy token
        $token_key = $_name.'_'.$_id;
        $token = md5(qSession::id().$token_key.qConfig::get('hash'));
        $_SESSION['tokens'][$token_key] = $token;

        return '<input type ="hidden" name="'.$_name.'[_token_]" value="'.$_SESSION['tokens'][$token_key].'" />'."\n";
    }

    public function html() {
        $xFormId = '';
        if (isset($this->attr->form_id)) {
            $xFormId = $this->attr->form_id;
        } else {
            $xFormId = $this->attr->id;
        }

        // początek formy
        $html = '<form';
        foreach ($this->attr->items() as $key => $value) {
            if ('#' != substr($key, 0, 1)) {
                if ('form_id' == $key) {
                } elseif ('id' == $key && isset($this->attr->form_id)) {
                    $html .= ' '.$key.'="'.$this->attr->form_id.'"';
                } else {
                    $html .= ' '.$key.'="'.$value.'"';
                }
            }
        }
        $html .= " >\n";
        if ('' != $this->comment) {
            $html .= '<!-- '.$this->comment.' -->'."\n";
        }
        // tabsy - nagłówek
        $htmlNag = '';
        $id = $this->attr->item('id');
        $class = $this->attr->item('class');
        $name = $this->attr->item('name');
        //foreach ($this->fields()->items() as $item) {
        //  Fbug::log($item);
        //	if ($item->type == FormField::TYPE_TAB) {
        //		$htmlNag .= "<li id=\"{$id}-header-item-{$item->item('#pageno')}\"";
        //		$htmlNag .= " class=\"{$class}-header-item\"><a href=\"#".$item->item('field')."\">{$item->item('label')}</a></li>\n";
        //	}
        //}
        //if ($htmlNag != '') {
        //	$html .= "<ul id=\"{$id}-header\" class=\"{$class}-header\">\n";
        //	$html .= $htmlNag;
        //	$html .= "</ul>\n";
        //}
        $html .= "<div id=\"{$id}-body\" class=\"{$class}-body\">\n";
        if ($this->token) {
            $html .= $this->setFormToken($name, $id);
        }
        foreach ($this->fields()->items() as $item) {
            if ($item->display()) {
                $html .= $item->html();
            }
        }
        $html .= "</div>\n";  // koniec bodu
        $html .= "</form>\n";
        //    $this->arrayValues();
        return $html;
    }

    public function setFromPost($params)
    {
        qLog::obsolete();
        $this->renderPost($params);
    }

    public function renderPost($params)
    {
        $result = array();
        $values = array();
        $tokenToCheck = false;
        $attribs = $this->attribs();
        $formIdentity = false;
        if (isset($attribs->token) && false == $attribs->token) {
        } else {
            if (isset($params['_token_'])) {
                $formIdentity = $attribs->item('name').'_'.$attribs->item('id');
                $tokenToCheck = $params['_token_'];
            }
            if ($tokenToCheck) {
                if (!isset($_SESSION['tokens'][$formIdentity]) || $_SESSION['tokens'][$formIdentity] !== $tokenToCheck) {
                    $_POST[$attribs->item('name')] = null;          // sfałszowany post-formularz - wyzeruj posta
                    return $result;
                //  ALBO poproś o hasło
                } else {
                    //unset($_SESSION['tokens'][$formIdentity]);     // przepuść dalej - token sprawdzony usuń
                }
            }
        }
        $items = $this->tree2item($params);
        foreach ($this->refFields as $key => $field) {
            if ($field->isValue()) {
                if (qFormField::TYPE_CHECKBOX == $field->type || qFormField::TYPE_SWITCHER == $field->type) {
                    $post_value = 0;
                    if (isset($items[$key])) {
                        $post_value = $items[$key];
                    }
                    $field->setItem('value', $post_value);
                    $result[$key] = $post_value;
                } elseif (qFormField::TYPE_CHECKBOXES == $field->type) {
                    if (isset($items[$key])) {
                        $val = $items[$key];
                        if (is_array($val)) {
                            foreach ($val as $xKey => $v) {
                                if (0 == $v) {
                                    unset($val[$xKey]);
                                }
                            }
                        }
                        $result[$key] = $val;
                    } else {
                        $val = array();
                    }
                    if ('new' != key($val) && 'fid' != key($val)) {
                        foreach ($field->select as $key => $value) {
                            if (in_array($key, $field->checked) && in_array($key, $field->readonly)) {
                                $val[$key] = $value;
                            }
                        }
                    }
                    $field->value = $val;
                } elseif (qFormField::TYPE_VALUE == $field->type) {
                } elseif (qFormField::TYPE_IMAGE == $field->type) {
                    if (isset($items[$key]['deleted']) && $items[$key]['deleted'] == 1) {
                        $items[$key]['fid'] = 0;
                    }
                    $field->setItem('value', $items[$key]);
                    $result[$key] = $items[$key];
                } else {
                    if (isset($items[$key])) {
                        $field->setItem('value', $items[$key]);
                        $result[$key] = $items[$key];
                    }
                }
            }
        }

        return $result;
    }

    public function preprocessValue($str)
    {
        $res = '';
        while (strlen($str) > 0) {
            $pos = strpos($str, '<');
            if (false === $pos) {
                $res .= $str;
                $str = '';
            } else {
                $res .= substr($str, 0, $pos);
                $str = substr($str, $pos + 1);
                $pos = strpos($str, '>');
                if (false !== $pos) {
                    $key = substr($str, 0, $pos);
                    $str = substr($str, $pos + 1);
                    if (isset($this->refFields[$key])) {
                        $field = $this->refFields[$key];
                        $res .= $field->item('value', '');
                    } else {
                        $res .= '#'.$key.'#';
                    }
                }
            }
        }

        return $res;
    }

    public function allFields()
    {
        // przeszukanie wszystkich pól
        $result = array();
        $this->_allFields($result, $items);

        return;
    }

    private function _allFields(&$result, $items)
    {
        $result = array();
        foreach ($this->items() as $key => $item) {
            if (is_object($item)) {
                if (qFormField::TYPE_WRAPPER == $item->type) {
                    $result[] = $item;
                } else {
                    $result[] = $item;
                }
            }
        }
    }

    public function setRefField($field)
    {
        $this->refFields[$field->field] = $field;
    }

    public function arrayFields()
    {
        return $this->refFields;
    }

    public function arrayValues()
    {
        $res = array();
        foreach ($this->refFields as $key => $fields) {
            $res[$key] = $fields->value;
        }

        return $res;
    }

    // Funkce prywaten

    /**
     * funkcja zmienia strukturę drzewasta parametrów w strukturę płaską z dostępem kropkowym.
     */
    public function tree2item($tree)
    {
        $items = array();
        $this->_tree2item($items, $tree, '');

        return $items;
    }

    private function _tree2item(&$items, &$tree, $keys)
    {
        foreach ($tree as $key => $item) {
            $items[$keys.$key] = $item;
            if (is_array($item)) {
                $this->_tree2item($items, $item, $keys.$key.'.');
            }
        }
    }

    private function item2tree($items)
    {
        $tree = array();
        foreach ($items as $key => $value) {
            $keys = explode('.', $key);
            $this->_item2tree($tree, $keys, $value);
        }

        return $tree;
    }

    private function _item2tree(&$tree, $keys, &$value)
    {
        $key = array_shift($keys);
        if (count($keys) > 0) {
            if (!isset($tree[$key])) {
                $tree[$key] = array();
            }
            $this->_item2tree($tree[$key], $keys, $value);
        } else {
            $tree[$key] = $value;
        }

        return $tree;
    }

    public function translate($fromLang, $toLang, $fields, $objFromLang = false)
    {
        $form = new FormTranslate($fromLang, $toLang);
        $form->generateFieldFromForm($this, $fields, $objFromLang);

        return $form;
    }

    public function transError($form)
    {
        foreach ($this->refFields as $key => &$field) {
            $field2 = $form->field($key);
            $field->error = $field2->error;
        }
    }

    public function validate($fields = false) {
        if ($fields === false) {
            $fields = $this->keys();
        } else {
            if (!is_array($fields)) {
                $fields = func_get_args();
            }
        }
        $result = true;
        foreach ($fields as $field) {
            $dots = explode('.', $field);
            $field = '';
            foreach ($dots as $dot) {
                $field .= ucfirst($dot);
            }
            $valid = 'field'.$field.'Validate';
            if (method_exists($this, $valid)) {
                if (!$this->$valid()) {
                    $result = false;
                }
            } 
        }

        return $result;
    }
    
    public function geValidatetErrors() {
        $result = [];
        $fields = $this->keys();
        foreach ($fields as $field) {
            $f = $this->item($field);
            if ($f->error) {
                $result[$field] = $f->error;
            }
            
        }
        return $result;
    }

    public function renderToken($bool)
    {
        $this->token = (true == $bool);
    }
}
