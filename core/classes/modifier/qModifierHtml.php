<?php

class qModifierHtml {
    public static function parse($str, $mod) {
        return htmlspecialchars($str);
    }
}
