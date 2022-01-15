<?php

class qFilter {
    
    protected $items = [];
    protected $values = '';
    protected $url = '';
    
    public function __construct($url) {
        $this->url = qHref::url($url);
        $sesKey = 'filter-'.str_replace('/', '-', $url);
        $filter = qCtrl::get('filter', '<none>');
        if ($filter !== '<none>') {
            qSession::set($sesKey, $filter);
            $this->setValues($filter);
        }
        else {
            $filter = qSession::get($sesKey, '<none>');
            $this->setValues($filter);
        }
        
    }

    public function addSelect($label, $name, $options) {
        $this->items[$name] = [
            'label' => $label,
            'type' => 'select',
            'options' => $options,
          ];
    }
    
    public function addText($label, $name ) {
        $this->items[$name] = [
            'label' => $label,
            'type' => 'text',
          ];
    }
    
    public function setValues($filter) {
        $values = [];
        foreach (explode(';', $filter) as $item) {
            $ex2 = explode(':', $item);
            if ($ex2[0] !== '') {
                if (count($ex2) == 1) {
                    $values[$ex2[0]] = '';
                }
                else {
                    $values[$ex2[0]] = $ex2[1];
                }
            }
        }
        $this->values = $values;
    }
    
    public function getValue($name, $default = '') {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }
        return $default;
    }

    public function render() {
        $html = '<div class="admin-filter">';
        $reset = false;
        foreach ($this->items as $name => $items) {
            if ($items['type'] === 'select') {
                $html .= '<label>'.$items['label'].'</label>';
                $html .= '<select name="'.$name.'">';
                $fValue = $this->getValue($name, '');
                if ($fValue != '') {
                    $reset = true;
                }
                foreach ($items['options'] as $key => $label) {
                    $selected = '';
                    if ($key === $fValue) {
                        $selected = ' selected="selected"';
                    }
                    $filter = $this->buildValues($name, $key);
                    if ($filter !== '') {
                        $url = $this->url.'?filter='.$filter;
                    }
                    else {
                        $url = $this->url.'?filter=';
                    }
                    $html .= '<option value="'.$url.'"'.$selected.'>'.$label.'</option>';
                }
                $html .= '</select>';
            }
            if ($items['type'] === 'text') {
                $fValue = $this->getValue($name, '');
                $filter = $this->buildValues($name, '');
                if ($filter !== '') {
                    $filter .= ';';
                }
                $html .= '<span class="search-text">';
                $html .= '<input class="text" placeholder="'.$items['label'].'" name="text" type="text" value="'.$fValue.'">';
                $html .= '<button class="filter-search" type="button">&#x1f50d;</button>';
                $html .= '<input class="url" name="url" type="hidden" value="'.$this->url.'?filter='.$filter.'">';
                $html .= '</span>';
            }
        }
        if ($reset) {
            $html .= '<span class="filter-reset"><a href="'.$this->url.'?filter='.'">Resetuj filtry</a></span>';
        }
        $html .= '</div>';
        return $html;
    } 
    
    public function buildValues($name = false, $value = false) {
        $result = '';
        $values = [];
        foreach ($this->values as $key => $val) {
            $values[$key] = $val;
        }
        if ($name !== false && $value === '') {
            if (isset($values[$name])) {
                unset($values[$name]);
            }
        }
        else {
            $values[$name] = $value;
        }
        foreach ($values as $key => $val) {
            if ($result !== '') {
                $result .= ';';
            }
            $result .= $key.':'.$val;
        }
        return $result;
    }
        
}