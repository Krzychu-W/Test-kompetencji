<div class="messages-list">
    <?php foreach($messages as $type => $items) { ?>
        <div class="message-block message-<?= $type; ?>">
            <?php
               $label = '';
               if ($type === 'info') {
                   $label = 'Informacja';
               }
               else if ($type === 'warning') {
                   $label = 'Ostrzeżenie';
               }
               else if ($type === 'Błąd') {
                   $label = 'Informacja';
               }
               else if ($type === 'Sukces') {
                   $label = 'Informacja';
               }
            ?>
            <h4><?= $label ?></h4>
            <ul>
                <?php foreach ($items as $message) { ?>
                    <li><?= $message; ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>