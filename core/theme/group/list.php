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
            <td><?= $item->name ?></td>
            <td class="action">
                <a class="edit" href="<?= qHref::link('group/edit/'.$item->id) ?>" title="Edit">Edit</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
