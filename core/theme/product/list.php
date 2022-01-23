<table class="table-admin">
    <thead>
    <tr>
        <th>Kod</th>
        <th>Nazwa</th>
        <th>Jednostka miary</th>
        <th>Akcja</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item) { ?>
        <tr>
            <td><?= $item->indeks ?></td>
            <td><?= $item->name ?></td>
            <td><?= $item->unit_name ?></td>
            <td class="action">
                <a class="edit" href="<?= qHref::link('product/edit/'.$item->id) ?>" title="Edit">Edit</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
