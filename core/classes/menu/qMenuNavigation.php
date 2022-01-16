<?php

class qMenuNavigation {

    private $items = [];

    public function addLink($title, $href, $active = false, $onclick = false) {
        $this->items[] = [
        'type' => 'link',
        'title' => $title,
        'href' => $href,
        'active' => $active,
        'onclick' => $onclick, 
        ];
    }
    
    public function addSelect($options) {
        $this->items[] = [
        'type' => 'select',
        'options' => $options,
        ];
    }

    public function get() {
        return $this->items;
    }

    public function render() {
        $block = new qTemplate();
        $block->items = $this->items;
        return $block->render('menu/navigation');
    }
}