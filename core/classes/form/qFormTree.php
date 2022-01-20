<?php

/*
 * Atrybuty zareówno forny jak i pól są dwojga rodzaju zwykłe i sterujące, które zaczynają się od #
 */

class qFormTree extends qItems
{
    private $actFieldset;
    private $form;
    private $fields;

    public function __construct(&$form)
    {
        // ustanowienie domyślnych atrybutów formy
        $this->actFieldset = false;
        $this->form = $form;
        $this->fields = new qItems();
    }

    /**
     * Nowa struktura.
     */
    public function setField($field)
    {
        $this->fields->setItem($field->field, $field); // zapis do gałęzi
    $this->form->setRefField($field);             // zapis do referencji
    }

    public function fields()
    {
        return $this->fields;
    }

    public function field($key)
    {
        return $this->fields->item($key, false);
    }

    public function __call($name, $args)
    {
        if ('items' == $name) {
            $field = new $name(get_object_vars($this->form)); 
        } else {
            $field = new $name($this->form);
            $fieldName = current($args);
            $key = key($args);
            unset($args[$key]);
        }
        $field->setItem('name', $fieldName);
        $this->setField($field);

        return $field;
    }

    public function addField($class, $param, $name = false)
    {
        $attr = new qAttr($param);
        if ($name) {
            $attr->name = $name;
        }
        $field = new $class($this->form);
        //$field->setItem('name',$wrapperName);
        $field->init($attr);
        $this->setField($field);

        return $field;
    }

    public function addTab($name)
    {
        $tab = new FormFieldTab($this->form);
        $tab->setItem('name', $name);
        $this->setField($tab);

        return $tab;
    }

    public function addGroup($name)
    {
        $group = new FormFieldGroup($this->form);
        $group->setItem('name', $name);
        $this->setField($group);

        return $group;
    }

    public function addFieldImage($name, $rows = array())
    {
        $field = new FormFieldImage($this->form);
        $field->setItem('name', $name);
        $field->setItem('rows', $rows);
        //$this->setItem($wrapperName,$field);
        $this->setField($field);

        return $field;
    }

    public function addFieldAlias($name, $rows)
    {
        $field = new FormFieldAlias($this->form);
        $field->setItem('name', $name);
        $field->setItem('rows', $rows);
        //$this->setItem($wrapperName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldHidden
     */
    public function addFieldHidden($fieldName)
    {
        $field = new FormFieldHidden($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldNode
     */
    public function addFieldNode($fieldName)
    {
        $field = new FormFieldNode($this->form);
        $field->setItem('name', $fieldName);
        $this->setField($field);

        return $field;
    }

    
    

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldHtml
     */
    public function addFieldHtml($fieldName)
    {
        $field = new FormFieldHtml($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldRadios
     */
    public function addFieldRadios($fieldName)
    {
        $field = new FormFieldRadios($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $name
     * @param $fieldName
     *
     * @return FormFieldRadio
     */
    public function addFieldRadio($name, $fieldName)
    {
        $field = new FormFieldRadio($this->form);
        $field->setItem('name', $name);
        //$field->setItem('radio_name',$fieldName);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldSelect
     */
    public function addFieldSelect($fieldName)
    {
        $field = new FormFieldSelect($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldInteger
     */
    public function addFieldInteger($fieldName)
    {
        $field = new FormFieldInteger($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecateds
     *
     * @param $fieldName
     *
     * @return FormFieldDatetime
     */
    public function addFieldDatetime($fieldName)
    {
        $field = new FormFieldDatetime($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldButton
     */
    public function addFieldButton($fieldName)
    {
        $field = new FormFieldButton($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }

    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldConfirm
     */
    public function addFieldConfirm($fieldName)
    {
        $field = new FormFieldConfirm($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }


    /**
     * @deprecated
     *
     * @param $fieldName
     *
     * @return FormFieldPassword
     */
    public function addFieldPassword($fieldName)
    {
        $field = new FormFieldPassword($this->form);
        $field->setItem('name', $fieldName);
        //$this->setItem($fieldName,$field);
        $this->setField($field);

        return $field;
    }


    /**
     * @deprecated
     *
     * @param bool|false $lang
     *
     * @return FormFieldHidden
     */
    public function addFieldLang($lang = false)
    {
        $field = new FormFieldHidden($this->form);
        $field->setItem('name', 'lang');
        if ($lang) {
            $field->value = $lang;
        }
        //$this->setItem('lang',$field);
        $this->setField($field);

        return $field;
    }
}
