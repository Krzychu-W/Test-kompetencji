<?php

class qFormSetting extends qForm {
    
    public $lang = true;
    public $code = [];
    public $noSaveEmpty = [];
    public $separatorCount = 0;

    public function __construct($params = []) {
        $this->setting();
        parent::__construct($params);
        $attribs = $this->attribs();
        $attribs->name = 'setting';
        
        $actions = $this->FormFieldActions('actions');
        
        $field = $actions->FormFieldSubmit('submit');
        $field->value = 'Zapisz';
        $field->class = 'btn btn-primary';
    }
    
    public function setting() {
        $this->lang = qCtrl::cLang();
    }

    public function addText($setting, $name, $default = '') {
        $fieldName = 'setting__'.$setting.'__'.$name;
        $field = $this->FormFieldText($fieldName);
        
        $field->value = qSettingLang::get($setting.'.'.$name, $default, $this->lang);
        return $field;
    }
    
    public function addPassword($setting, $name, $default = '') {
        $fieldName = 'setting__'.$setting.'__'.$name;
        $field = $this->FormFieldPassword($fieldName);
        $field->value = qSettingLang::get($setting.'.'.$name, $default, $this->lang);
        return $field;
    }
    
    public function addTextArea($setting, $name, $default = '') {
        $fieldName = 'setting__'.$setting.'__'.$name;
        $field = $this->FormFieldTextarea($fieldName);
        $field->value = qSettingLang::get($setting.'.'.$name, $default, $this->lang);
        return $field;
    }
    
    public function addCheckbox($setting, $name, $default = 0) {
        $fieldName = 'setting__'.$setting.'__'.$name;
        $field = $this->FormFieldCheckbox($fieldName);
        $field->value = qSettingLang::get($setting.'.'.$name, $default, $this->lang);
        return $field;
    }
    
    public function addSelect($setting, $name, $default = '') {
        $fieldName = 'setting__'.$setting.'__'.$name;
        $field = $this->FormFieldSelect($fieldName);
        $field->value = qSettingLang::get($setting.'.'.$name, $default, $this->lang);
        return $field;
    }
    
    public function addSeparator($label) {
        $this->separatorCount++;
        $fieldName = 'separator__'.$this->separatorCount;
        $field = $this->FormFieldHtml($fieldName);
        $field->block = '<h3>'.$label.'</h3>';
        return $field;
    }
 

    public function save() {
        foreach ($this->values() as $key => $value) {
            $ex = explode('__', $key);
            if ($ex[0] == 'setting') {
                unset($ex[0]);
                if (isset($ex[1])) {
                    $name = $ex[1];
                    unset($ex[1]);
                    $field = implode('__', $ex);
                    $name .= '.'.$field;
                    if ($value || !in_array($field, $this->noSaveEmpty)) {
                        if (in_array($field, $this->code)) {
                            qSettingLang::set($name, $value, $this->lang, true);
                        }
                        else {
                            qSettingLang::set($name, $value, $this->lang);
                        }
                    }
                        
                }
            }
        }
    }
}