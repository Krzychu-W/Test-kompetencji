<!DOCTYPE html>
<html lang="<?= qCtrl::lang(); ?>">
    <head>
        <meta charset="UTF-8">
        <title><?= qLayout::metaTitle() ?></title>
        <meta name="title" content="<?= qLayout::metaTitle() ?>" />
        <meta property="og:title" content="<?= qLayout::metaTitle() ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?= qLayout::metaPrint() ?>
        <?= qLayout::linkPrint() ?>
        <?php
            qLayout::css('core://stylesRoot.css');
            qLayout::css('core://stylesMessage.css');
            qLayout::css('core://stylesBtn.css');
            qLayout::css('core://stylesClick.css');
            qLayout::css('core://stylesOverlay.css');
            qLayout::css('core://stylesMainMenu.css');
            qLayout::css('core://stylesTable.css');
            qLayout::css('core://styles99.css');
            qLayout::css('core://regStyles.css');
            qLayout::css('volume://volume.css');
            echo qLayout::cssPrint()
        ?>
    </head>
    <body class="<?= qLayout::classes(); ?>" lang="<?= qCtrl::lang(); ?>">
        <div class="top">
            <div class="top-flex">
                <button id="three-dash" class="three-dash">
                      <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                           height="40px" width="40px" viewBox="0 0 40 40">
                        <path d="M2 3H38V10H2z M2 16H38V23H2z M2 29H38V36H2z"></path>
                      </svg>
                </button>
                <span class="title">Test kompetencji</span>
            </div>
        </div>
        <div class="root">
            <div style="height:40px;">&nbsp;</div>
            <header><?= qMessage::display(); ?></header>
            <main>
                <?php if (qLayout::has('navigation')) { ?>
                    <div id="navigation"><?= qLayout::get('navigation') ?></div>
                <?php } ?>
                <?= qBreadcrumb::render() ?>
                <article>
                    <h1 class="main-title"><?= qLayout::title() ?></h1>
                    <?php if (qLayout::has('submenu')) { ?>
                        <div id="submenu"><?= qLayout::get('submenu') ?></div>
                    <?php } ?>
                    <section><?= qLayout::get('content','-?-'); ?></section>
                </article>
            </main>
            <div id="overlay" class="close">
                <div id="overlay-content-wrapper">
                    <span id="overlay-close" onclick="qAnt.ajax.closeOverlay()">&#x2716;</span>
                    <div class="overlay-content"></div>
                </div>
            </div>
            <div id="bottom-message">
                <span id="bottom-message-close">&#x2716;</span>
                <div id="bottom-message-top">PRZYPISY</div>
                <div id="bottom-message-content" data-ref="0"></div>
            </div>
            <footer>
                Kontakt
            </footer>
        </div>
        <?php
            qLayout::js('core://main.js');
            qLayout::js('core://admin.js');
            echo qLayout::jsPrint()
        ?>
    </body>
    <?= qLayout::developPrint() ?>
</html>