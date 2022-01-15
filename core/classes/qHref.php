<?php

class qHref
{
    /**
     * Funkcja generuje adres url do strony wewnetrzej.
     *
     * @param string $link    Link w postaci: action/method/opcja1/...
     * @param array  $aParams Parametry GET w postaci tablicy asocjacyjnej, domyślnie pusty
     * @url   bool|string
     *        false - link krótki
     *        true - pełny link
     *        string -  nazwa serwera, np. http://struktury.net
     *
     * @return string url
     */
    public static function url($link, $aParams = [], $url = false)
    {
        $links = explode('/', $link);
        $lang = qCtrl::lang();
        if (count($links) > 0 && 2 == strlen($links[0])) {
            $lang = $links[0];
            unset($links[0]);
            $link = implode('/', $links);
        }
        return self::urlLang($lang, $link, $aParams, $url);
    }

    /**
     * Funkcja generuje adres url do strony wewnętrznej.
     *
     * @param string $lang    język
     * @param string $link    link
     * @param array  $aParams Parametry GET (=array())
     * @param @url   bool|string
     *        false - link krótki
     *        true - pełny link
     *        string -  nazwa serwera, np. http://struktury.net
     */
    public static function urlLang($lang, $link, $aParams = [], $url = false)
    {
        $parsedUrl = parse_url($link);
        if (!empty($parsedUrl['path'])) {
            $link = $parsedUrl['path'];
        }
        if (!empty($parsedUrl['query'])) {
            $queryString = $parsedUrl['query'];
        } else {
            $queryString = '';
        }

        // szukaj przyjaznego adresy

        if ($lang) {
            if (qConfig::get('lang.show.default') || $lang != qConfig::get('lang')) {
                $link = $lang.'/'.$link;
            }
        }
        $link = str_replace(',', '/', $link);
        $link = preg_replace("/\/+/", '/', $link);
        if ($url === true) {
            $link = qConfig::get('url.base').'/'.$link;
        }
        else if($url === false) {
            $link = '/'.$link;
        }
        else {
            $link = $url.'/'.$link;
        }

        if (empty($queryString) && count($aParams) > 0) {
            $param = '';
            foreach ($aParams as $key => $val) {
                if ('' !== $param) {
                    $param .= '&';
                }
                $param .= $key.'='.$val;
            }
            $link .= '?'.$param;
        } elseif (!empty($queryString)) {
            $link .= '?'.$queryString;
        }
        return $link;
    }

    /**
     * Link skrócony do akcji, bez nazwy serwera z początkowym slesh
     * 
     * @param string $link
     * @param array $params - parametry get
     * @param boolean
     * @return string
     */
    public static function sLink($link, $params = [], $addLang = true) {
        $links = explode('/', $link);
        $lang = qConfig::get('lang');
        if (count($links) > 0 && strlen($links[0]) === 2) {
            $lang = $links[0];
            unset($links[0]);
            $link = implode('/', $links);
        }
        $lang = qCtrl::lang();
        if ($lang && $addLang) {
            if (qConfig::get('lang.show.default') || $lang != qConfig::get('lang')) {
                $link = $lang.'/'.$link;
            }
        }
        $link = '/'.$link;
        if (count($params) > 0) {
            $param = '';
            foreach ($params as $key => $val) {
                if ('' !== $param) {
                    $param .= '&';
                }
                $param .= $key.'='.$val;
            }
            $link .= '?'.$param;
        }

        return $link;
    }
    
    /**
     * Link do strony dla pełną nazwą protokołu i nazwy serwera
     * 
     * @param string $link
     * @param array $params
     * @param boolean $addLang
     * @return string
     */
    
    public static function link($link, $params = [], $addLang = true) {
        return qConfig::get('url.base').self::sLink($link, $params, $addLang);
    }

    /**
     * Zwraca link do uploadowanego pliku.
     *
     * @param string $file Nazwa pliku
     *
     * @return string Link do pliku
     */
    public static function upload($file)
    {
        $link = qConfig::get('url.upload').'/'.$file;

        return $link;
    }

    /**
     * Parsuje adres sprawdzajac przedrostek
     * public://, upload://, module://.
     *
     * @param string $href URL
     *
     * @return string URL
     */
    public static function parse($href)
    {
        if (qString::strBegin($href, 'public://')) {
            $href = qConfig::get('url.public').'/'.qString::cutBegin($href, 'public://');
        } elseif (qString::strBegin($href, 'module://')) {
            $href = qConfig::get('url.base').'/module/'.qString::cutBegin($href, 'module://');
        } elseif (qString::strBegin($href, 'upload://')) {
            $href = qConfig::get('url.upload').'/'.qString::cutBegin($href, 'upload://');
        } elseif (qString::strBegin($href, 'upload2://')) {
            $href = qConfig::get('url.upload2').'/'.qString::cutBegin($href, 'upload2://');
        }

        return $href;
    }

    public static function rebildUrl($newParam = array())
    {
        $link = qCtrl::oryginal();
        $post = array();
        foreach (qCtrl::items() as $key => $value) {
            $post[$key] = $value;
        }
        foreach ($newParam as $key => $value) {
            $post[$key] = $value;
        }
        if (count($post) > 0) {
            $get = '';
            foreach ($post as $key => $value) {
                if ('' != $get) {
                    $get .= '&';
                }
                $get .= $key.'='.$value;
            }
            $link .= '?'.$get;
        }
        if ('/' != substr($link, 0, 1)) {
            $link = '/'.$link;
        }

        return $link;
    }
}

