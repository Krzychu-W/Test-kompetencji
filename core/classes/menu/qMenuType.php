<?php

class qMenuType {
    
    static $items;
    
    public static function init() {
        self::$items = [
            'internal' => 'wewnętrzny',
            'external' => 'zewnętrzy',
            'firstChild' => 'pierwszy element',
        ];
    }
    
    public static function options() {
        return self::$items;
    }
    
    public static function getLabel($key) {
        if (array_key_exists($key, self::$items)) {
            return self::$items[$key];
        }
        return $key;
    }
    
}

qMenuType::init();