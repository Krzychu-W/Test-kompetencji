<?php $ii = 0;
foreach ($submenu as $key => $item) {
    $class = 'btn';
    if ($ii == 0) {
        $class .= ' flex-left';
    } 
    else {
        $class .= ' flex-right';
    }
    if ($item->active) {
        $class .= ' active';
    }
    if ($item->onclick) {
        echo '<a class="'.$class.'" onclick="'.$item->onclick.'" href="'.$item->href.'">'.$item->title.'</a>';
    }
    else {
        echo '<a class="'.$class.'" href="'.$item->href.'">'.$item->title.'</a>';
    }
    $ii++;
} ?>