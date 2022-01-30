<table class="table-admin">
    <thead>
    <tr>
        <th>Nazwa</th>
        <th>Akcja</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item) { ?>
        <tr>
            <td><?= str_repeat('&nbsp;&nbsp;&nbsp;',$item->deep-1).'+&nbsp;&nbsp;'.$item->name ?></td>
            <td class="action">
                <a class="edit" href="<?= qHref::link('group/edit/'.$item->id) ?>" title="Edit">Edit</a>
                <a class="add" href="<?= qHref::link('group/new/'.$item->id) ?>" title="New">Dodaj podkategorię</a>
                <a class="addp" href="<?= qHref::link('product/new/'.$item->id) ?>" title="New">Dodaj produkt</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
