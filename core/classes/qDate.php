<?php

/* 
 * Przekstalcenia daty
 */

class qDate {
    
    protected $dateString;
    protected $precision;

    public function __construct($date = null) {
        $this->dateString = $date;
        if (strlen($date) == 4) {
            $this->precision = 'Y';
        }
        else if (strlen($date) == 7) {
            $this->precision = 'M';
        }
        else {
            $this->precision = 'D';
        }
    }

    public function plusDay($d = 1) {
        if ($this->precision === 'Y') {
            $str = $this->dateString.'-01-01';
        }
        else if ($this->precision === 'M') {
            $str = $this->dateString.'-01';
        }
        else {
            $str = $this->dateString;
        }
        $time = strtotime($str);
        $time += (86400 * $d) + 21600; // ze wzglrdu na zmianę czasu
        if ($this->precision === 'Y') {
            return date('Y', $time);
        }
        if ($this->precision === 'M') {
            return date('Y-m', $time);
        }
        return date('Y-m-d', $time);
    }
    
    public function minusDay($d = 1) {
        return $this->plusDay(-$d);
    }
    
    public function plusMonth($m = 1) {
        return $this->changeMonth($m);
    }
    
    public function minusMonth($m = 1) {
        return $this->changeMonth(-$m);
    }
    
    public function plusYear($y = 1) {
        return $this->changeMonth($y);
    }
    
    public function minusYear($y = 1) {
        return $this->changeMonth(-$y);
    }
    
    protected function changeMonth($m) {
        if ($this->precision === 'Y') {
            $str = $this->dateString.'-01-01';
        }
        else if ($this->precision === 'M') {
            $str = $this->dateString.'-01';
        }
        else {
            $str = $this->dateString;
        }
        $y1 = (integer)substr($str, 0, 4);
        $m1 = (integer)substr($str, 5, 2);
        $d1 = (integer)substr($str, 8, 2);
        if ($m > 0) {
            $m1 += $m;
            while ($m1 > 12) {
                $m1 -= 12;
                $y1++;
            }
        }
        else if ($m < 0) {
            $m1 += $m;
            while ($m1 < 1) {
                $m1 += 12;
                $y1--;
            }
        }
        $str = qString::strPad0($y1, 4).'-'.qString::strPad0($m1, 2);
        if ($this->precision === 'D') {
            $lastDay = (integer)date('t', strtotime($str.'-01'));
            if ($lastDay < $d1) {
                $str .= '-'.qString::strPad0($lastDay, 2);
            }
            else {
                $str .= '-'.qString::strPad0($d1, 2);
            }
            return $str;
        }
        if ($this->precision === 'M') {
            return $str;
        }
        return substr($str, 0, 4);
    }
    
    protected function changeYear($y) {
        if ($this->precision === 'Y') {
            $y1 = (integer)$this->str;
            $y1 += $y;
            return qString::strPad0($y1, 4);
        }
        else if ($this->precision === 'M') {
            $str = $this->dateString.'-01';
        }
        else {
            $str = $this->dateString;
        }
        $y1 = (integer)substr($str, 0, 4);
        $m1 = (integer)substr($str, 5, 2);
        $d1 = (integer)substr($str, 8, 2);
        $y1 += $y;
        $str = qString::strPad0($y1, 4).'-'.qString::strPad0($m1, 2);
        if ($this->precision === 'D') {
            $lastDay = (integer)date('t', strtotime($str.'-01'));
            if ($lastDay < $d1) {
                $str .= '-'.qString::strPad0($lastDay, 2);
            }
            else {
                $str .= '-'.qString::strPad0($d1, 2);
            }
            return $str;
        }
        return $str;
    }
}

