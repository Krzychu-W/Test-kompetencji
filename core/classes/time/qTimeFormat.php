<?php

class qTimeFormat {
    
    public static function toDhm($time) {
        return date('Y-m-d H:i', $time);
    }
    
    public static function intervalToString($interval) {
        $d = '';
        $h = '00';
        $m = '00';
        $s = '00';
        if ($interval < 60) {
            // poniżej 60 sekund
            $s = qString::strPad0($interval, 2);
        }
        else {
            // powyżej 60 sekund
            $mx = floor($interval / 60);
            $s = qString::strPad0($interval - $mx*60, 2);
            if ($mx < 60) {
                // poniżej 60 minut
                $m = qString::strPad0($mx, 2);
            }
            else {
                // powyżej 60 minut
                $hx = floor($mx / 60);
                $m = qString::strPad0($mx - $hx*60, 2);
                if ($hx < 24) {
                    // poniżej 24 godzin
                    $h = qString::strPad0($hx, 2);  
                }
                else {
                    $d = floor($hx / 24).'d&nbsp;';
                    $h = qString::strPad0($hx - $d*24, 2);
                }
            }
        }
        if ($d > 0) {
            if ($h == '00' && $m == '00' && $s == '00') {
                return $d;
            }
        }
        return trim($d.$h.':'.$m.':'.$s);
    }
    
}

