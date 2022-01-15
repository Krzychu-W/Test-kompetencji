<?php
if (count($items)>0 || $current != '') { ?>
<div class="breadcrumb"><a class="first" href="<?= $homeUrl ?>"><svg width="14px" height="14px" viewBox="0 0 1024 1024" style="">
<path d="M1024 590.444l-512-397.426-512 397.428v-162.038l512-397.426 512 397.428zM896 576v384h-256v-256h-256v256h-256v-384l384-288z"></path>
</svg></a><?php foreach ($items as $url => $title ) { ?>&nbsp;&raquo;&nbsp;<a href="<?= $url ?>"><?= $title ?></a><?php }
if ($current && $currentUrl) { ?>&nbsp;&raquo;&nbsp;<span><a href="<?= $currentUrl ?>"><?= $current ?></a></span>
<?php } else if ($current) { ?>
&nbsp;&raquo;&nbsp;<span><?= $current ?></span><?php } ?></div>
<?php } ?>