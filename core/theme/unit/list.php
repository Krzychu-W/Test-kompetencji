<table class="table-admin">
    <thead>
    <tr>
        <th>Nazwa</th>
        <th>Skrót</th>
        <th>Użycie</th>
        <th>Akcja</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item) { ?>
        <tr>
            <td><?= $item->name ?></td>
            <td><?= $item->short ?></td>
            <td><?= $item->count ?></td>
            <td class="action">
                <a class="edit" href="<?= qHref::link('unit/edit/'.$item->id) ?>" title="Edit">Edit</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
