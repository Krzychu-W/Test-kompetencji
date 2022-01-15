<?php

class qSubmenu {
    private $submenu;


    public function __construct() {
        $this->submenu = array();
    }

    public function add($title, $href, $active = false, $onclick = false) {
        $this->submenu[] = (object) array(
        'title' => $title,
        'href' => $href,
        'active' => $active,
        'onclick' => $onclick, );
    }

    public function get() {
        return $this->submenu;
    }

    public function render() {
        $block = new qTemplate();
        $block->submenu = $this->submenu;
        return $block->render('menu/submenu');
    }
}
