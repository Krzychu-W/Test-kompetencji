<?php

class qMenu
{
    public static $activeHref = array();

    public static function setActiveHref($href)
    {
        $type = 'http';
        if ('http' == $type || 'https' == $type) {
            $type .= '://';
        } elseif ('http:' == $type || 'https:' == $type) {
            $type .= '//';
        }
        $len = strlen($type);
        if (substr($href, 0, $len) != $type) {
            $href = qHref::url($href);
        }
        if (!in_array($href, self::$activeHref)) {
            self::$activeHref[] = $href;
        }
    }

    public static function delActiveHref($href)
    {
        $type = 'http';
        $len = strlen($type);
        if (substr($href, 0, $len) != $type) {
            $href = baseUrl().'/'.$href;
        }
        foreach (self::$activeHref as $key => $value) {
            if ($value == $href) {
                unset(self::$activeHref[$key]);
            }
        }
    }

    public static function getActiveHref($href = false)
    {
        if ($href) {
            self::setActiveHref($href);
        }

        return self::$activeHref;
    }
}
