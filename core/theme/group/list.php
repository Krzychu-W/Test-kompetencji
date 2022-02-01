<p>Nowy produkt można dodać tylko do grupy, która nie ma podgrupy (liścia).</p>
<p>Podgrupę można dodać tylko do grupy, która nie ma produktów.</p>
<p>Walidacja na trzech poziomach:</p>
<ul>
    <li>ograniczenia w linkach na tej stronie</li>
    <li>podczas edycji/dodawania grupy ograniczenie w wyborze drzewa (w select)</li>
    <li>kontrola ostateczna to walidacja rekordu przez zapisem do bazy (kontrola właściwa)</li>
</ul>
<p>Drzewo pobierane na nowe okno w postaci zrzutu obiektu.</p>
<table class="table-admin">
    <thead>
    <tr>
        <th>Nazwa</th>
        <th>Ilość produktów</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item) { ?>
        <tr>
            <td><?= str_repeat('&nbsp;&nbsp;&nbsp;',$item->deep-1).'+&nbsp;&nbsp;'.$item->name ?></td>
            <td><?= $item->prods ?></td>
            <td class="action">
                <a class="edit" href="<?= qHref::link('group/edit/'.$item->id) ?>" title="Edit">Edit</a>
            </td>
            <td class="action">
                <?php if ($item->prods == 0) { ?>
                <a class="add" href="<?= qHref::link('group/new/'.$item->id) ?>" title="New">Dodaj podgrupę</a>
                <?php } else {?>
                    &nbsp;
                <?php } ?>
            </td>
            <td class="action">
                <?php if ($item->sub == 0) { ?>
                <a class="addp" href="<?= qHref::link('product/new/'.$item->id) ?>" title="New">Dodaj produkt</a>
                <?php } else {?>
                    &nbsp;
                <?php } ?>
            </td>
            <td class="action">
                <a class="tree" target="_blank" href="/group/tree/<?= $item->id ?>" title="Get tree">Pobierz drzewo</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<a class="tree" target="_blank" href="/group/tree/0" title="Get tree">Pobierz całe drzewo</a>