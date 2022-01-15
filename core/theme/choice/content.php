<div class="module-choice">
<h2>{$question}</h2>
<ul>
<?php foreach ($items as $item) { ?>
<li><a href="<?= $item->link ?>"><?= $item->value ?></a></li>
<?php } ?>
</ul>
<?php if ($description) { ?>
<?= $description ?>
<?php } ?>
</div>