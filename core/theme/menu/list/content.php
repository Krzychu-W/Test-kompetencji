<?php
$anonimus = $menu->getItem('homeAnonimus', '');
if ($anonimus == '') {
    $anonimus = 'index/home';
}
$registred = $menu->getItem('homeRegistred', '');
if ($registred == '') {
    $registred = $anonimus;
} ?>
<table>
<tbody>
<tr>
<td><strong>Strona główna dla niezalogowanych</strong></td>
<td><a href="/<?= $anonimus ?>"><?= $anonimus ?></a></td>
<td class="action">
  <a class="edit" href="/menu/item/main/<?= $menu->getAcronym() ?>/anonimus" title="Edit">Edit</a>
</td>
</tr>
<tr>
<td><strong>Strona główna dla zalogowanych</strong></td>
<td><a href="/<?= $registred ?>"><?= $registred ?></a></td>
<td class="action">
  <a class="edit" href="/menu/item/main/<?= $menu->getAcronym() ?>/registred" title="Edit">Edit</a>
</td>
</tbody>
</table>
<hr>
<table class="table-ordering">
<thead>
<tr>
<th><?= qTrans::get('menu.list-title'); ?></th>
<th><?= qTrans::get('menu.list-link'); ?></th>
<th><?= qTrans::get('menu.list-status'); ?></th>
<th><?= qTrans::get('action'); ?></th>
<th><?= qTrans::get('menu.list-weight'); ?></th>
</tr>	
</thead>
<tbody>
<?php foreach($menu->elements as $key => $row) { 
// elementy przygotowawcze
?>
<tr>
<td><?= str_repeat('&nbsp;', $row['deep']*3).'&#9632;' ?>
  <?php if ($row['type'] == 'firstChild') {
      echo $row['label'];
  }
  else { ?>
      <a href="<?= qHref::url($row['link']); ?>" title="<?= $row['alt'] ?>"><?= $row['label'] ?></a>
  <?php } ?></td>
<td><?php if ($row['type'] == 'firstChild') {echo 'firstChild';} else {echo $row['link'];} ?></td>
<td><?= $row['status'] ?></td>
<td class="action">
  <a class="edit" href="/menu/item/edit/<?= $menu->getAcronym() ?>/<?= $key ?>" title="Edit">Edit</a>
  <a class="delete" href="/menu/item/delete/<?= $menu->getAcronym() ?>/<?= $key ?>" 
     title="Delete" onclick="return qAnt.menu.delete('<?= $menu->getAcronym() ?>',<?= $key ?>)">Delete</a>
</td>
<td>
  <a onclick="return qAnt.menu.left(<?= $key ?>)">&#9664;</a>
  <a onclick="return qAnt.menu.right(<?= $key ?>)">&#9654;</a>
  <a onclick="return qAnt.menu.up(<?= $key ?>)">&#9650;</a>
  <a onclick="return qAnt.menu.down(<?= $key ?>)">&#9660;</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
