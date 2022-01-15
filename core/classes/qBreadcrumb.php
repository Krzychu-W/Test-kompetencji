<?php
/**
 * Moduł Breadcrumb służy do generowania okruszków.
 *
 * @author Krzysztof Wałek
 */
class qBreadcrumb {
    
    public static $home = '';
    public static $homeUrl = '';
    public static $items = array();
    public static $current = '';
    public static $currentUrl = false;

    public static function reset() {
        self::$home = 'Home';
        self::$homeUrl = '';
        self::$items = array();
        self::$current = '';
        self::$currentUrl = false;
    }

    public static function setTpl($tpl) {
        self::$tpl = $tpl;
    }

    public static function setHome($home) {
        self::$home = $home;
    }

    public static function setHomeUrl($homeUrl) {
        self::$homeUrl = $homeUrl;
    }

    public static function getItems() {
        return self::$items;
    }

    public static function setItems($items) {
        self::$items = $items;
    }

    public static function setCurrent($current) {
        self::$current = $current;
    }

    public static function addItem($url, $title) {
        $url = qHref::url($url);
        self::$items[$url] = $title;
    }

    public static function setCurrentUrl($currentUrl) {
        self::$currentUrl = $currentUrl;
    }

    public static function render() {
        $block = new qTemplate();
        $block->home = self::$home;
        $block->homeUrl = qHref::url(self::$homeUrl);
        $block->items = self::$items;
        $block->current = self::$current;
        $block->currentUrl = self::$currentUrl;
        return $block->render('breadcrumb/breadcrumb');
    }

    
}
