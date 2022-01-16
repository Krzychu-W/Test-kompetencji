<?php

class qMenuAdmin {

    protected static $items = [];

    public static function init() {
        $itemsAll = [];
        $items = [];

        $items[] = [
            'title' => 'Grupy materiałów',
            'class' => 'group',
            'link' => 'group/list',
        ];
        $items[] = [
            'title' => 'Materiały',
            'class' => 'product',
            'link' => 'product/list',
        ];
        $items[] = [
            'title' => 'Jednostki miary',
            'class' => 'unit',
            'link' => 'unit/list',
        ];


        if (count($items) > 0){
            $itemsAll[] = [
                'title' => 'Użytkownik: ',
                'class' => 'user',
                'items' => $items,
            ];
        }


        foreach ($itemsAll as &$items) {
            foreach ($items['items'] as &$item) {
                $item['href'] = false;
            }
        }
        self::$items = $itemsAll;
    }

    public static function render() {
        $itemsAll = self::$items;
        foreach ($itemsAll as $key => $items) {
            foreach ($items['items'] as &$item) {
                $link = $item['link'];
                $item['href'] = qHref::url($link);
            }
            foreach (qMenu::getActiveHref(qHref::url(qCtrl::oryginal())) as $href) {
                $items['items'] = self::setActive($items['items'], $href);
            }
            $itemsAll[$key] = $items;
        }
        $result = '';
        foreach ($itemsAll as $items) {
            $content = '<strong>'.$items['title'].'</strong>';
            foreach ($items['items'] as $item1) {
                $aClass = '';
                if ($item1['active']) {
                    $aClass = ' class="active"';
                }
                $content .= "<a href=\"{$item1['href']}\" {$aClass}>{$item1['title']}</a>";
            }
            $result .= '<li class="'.$items['class'].'">'.$content.'</li>'."\n";
        }
        if ($result !== '') {
            $result = "<ul>\n".$result."</ul>\n";
        }

        $block = new qTemplate();
        $block->html = $result;
        return $block->render('menu/admin');
    }

    private static function setActive($items, $value) {
        foreach ($items as &$item) {
            if ($item['href'] == $value) {
                $item['active'] = true;
            } else {
                $item['active'] = false;
            }
        }

        return $items;
    }

}

qMenuAdmin::init();
