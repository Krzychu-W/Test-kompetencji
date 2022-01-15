<?php

class qDevelop {
    
    public static function comment() {
        $result = '';
        $result .= 'volume: '.qConfig::get('volume');
        $result .= ', time: '.number_format(qTimeExec::total(3), 3, '.', '').' s';
        $result .= ', memory: '.number_format((float) round((memory_get_peak_usage(false) / 1024) / 1024, 2), 2)
               .'/'.number_format((float) str_replace('M', '', ini_get('memory_limit')), 2);
        $result .= ', orginal: '.qCtrl::original();
        return '<!-- '.$result. ' -->'."\n";

    }
    
}
