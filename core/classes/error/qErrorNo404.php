<?php

class qErrorNo404 {
    
    public function render() {
        qLayout::title('Page 404');
        $html = '<h1>Taka strona nie istnieje</h1>';
        $html .= '<p>Wpisałeś błędny adres</p>';
        qLayout::set('content', $html);
    }
}

