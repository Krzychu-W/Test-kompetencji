<?php

class qArraySort {
    
    static $field;
    
    public static function ascByIntFieldObj($items, $field) {
        self::$field = $field;
        usort($items, ['qArraySort', '__ascObjInt']);
        return $items;
    }
    
    public static function descByIntFieldObj($items, $field) {
        self::$field = $field;
        usort($items, ['qArraySort', '__descObjInt']);
        return $items;
    }
    
    public static function ascByStringFieldObj($items, $field) {
        self::$field = $field;
        usort($items, ['qArraySort', '__ascObjString']);
        return $items;
    }
    
    public static function descByStringFieldObj($items, $field) {
        self::$field = $field;
        usort($items, ['qArraySort', '__descObjString']);
        return $items;
    }
    
    public static function __ascObjInt($a, $b) {
        $field = self::$field;
        return $a->$field > $b->$field;
        
    }
    
    public static function __descObjInt($a, $b) {
        $field = self::$field;
        return $a->$field < $b->$field;
    }
    
    public static function __ascObjtring($a, $b) {
        $field = self::$field;
        return strcmp($a->$field, $b->$field);
        
    }
    
    public static function __descObjtring($a, $b) {
        $field = self::$field;
        return -strcmp($a->$field, $b->$field);
    }
    
    public static function lastKey($array) {
        $keys = array_reverse(array_keys($array));
        if (isset($keys[0])) {
            return $keys[0];
        }
        return null;
    } 
}