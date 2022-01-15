<div id="menu-navigation" class="menu-navigation">
<ul>
<?php
foreach ($items as $key => $item) {
    $class = 'navigation-btn';
    if ($item['active']) {
        $class .= ' active';
    }
    echo '<li class="'.$class.'">';
    if ($item['type'] === 'link') {
        if ($item['onclick']) {
            echo '<a class="'.$class.'" onclick="'.$item['onclick'].'" href="'.$item['href'].'">'.$item['title'].'</a>';
        }
        else {
            echo '<a class="'.$class.'" href="'.$item['href'].'">'.$item['title'].'</a>';
        }
    }
    else if ($item['type'] === 'select') {
       echo '<select>';
       foreach ($item['options'] as $link => $name) {
         echo '<option value="'.$link.'">'.$name.'</option>';
       }
       echo '</select>';
    } 
    echo '</li>';
} ?>
</ul>
</div>