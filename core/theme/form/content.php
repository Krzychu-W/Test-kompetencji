<?php 
    if(is_array($class)) {
        $class = ' class="'.implode(' ', $class).'"';
    }
    else if (is_string($class)) {
        $class = ' class="'.$class.'"';
    }
    else {
        $class = '';
    }
?>
<div<?= $class ?>>
<?= $form->getForm(); ?>
</div>
