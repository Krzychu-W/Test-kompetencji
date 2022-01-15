<div class="messages-list">
    <?php foreach($messages as $type => $items) { ?>
        <div class="message-block message-<?= $type; ?>">
            <h4><?= qTrans::get('message.'.$type); ?></h4>
            <ul>
                <?php foreach ($items as $message) { ?>
                    <li><?= $message; ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>