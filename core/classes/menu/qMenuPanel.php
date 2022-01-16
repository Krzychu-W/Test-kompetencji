<?php

class qMenuPanel {
    public static $instance;

    protected $admin = '';

    public static function init() {
        
        self::$instance = new self();
        self::$instance->admin = trim(qMenuAdmin::render());

    }

    public static function renderUser() {
        $block = new qTemplate();
        $block->admin = self::$instance->admin;
        return $block->render('menu/user');
    }

}

qMenuPanel::init();
