<div class="message <?php if ($danger) { echo 'colorbox-error-message'; } else { echo 'colorbox-choice-message'; }?>">
<span class="icon"></span>
<div class="txt"><div><h4><p><?= $question ?></p></h4></div></div>
<?php if ($description) { ?><div><?= $description ?></div><?php } ?>
<?php if( count($checkbox) > 0) { ?>
<form id="form-choice" class="form" name="choice" method="post" accept-charset="UTF-8">
<div id="form-body" class="form-body">
<div id="form-field-choice-wrapper" class="form-field-checkboxes-wrapper">
<div class="form-field-checkboxes-content form-field-content">
<div class="form-checkboxes">
<?php foreach($checkbox as $key => $label) { ?>  
<div class="form-checkboxes-item">
<input id="choice-checkbox-<?= $key ?>" name="choice[<?= $key ?>]" value="1" type="checkbox">
<label for="choice-checkbox-<?= $key ?>" class="checkboxs-item"><?= $label ?></label>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
</form>
<?php } ?>
<div class="btns<?php if (count($items) === 2){ echo 'btns2'; } ?>">
<?php foreach($items as $key => $item) {
  $class = "btn btn{$key}";
  if ($item->danger) {
      $class .= ' btn-danger';
  }
  if ($item->success) {
      $class .= ' btn-success';
  } ?>
<a class="<?= $class ?>" onclick="<?= $item->onclick ?>"><?= $item->value ?></a> 
<?php } ?>
</div>
</div>