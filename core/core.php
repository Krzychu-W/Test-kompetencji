<?php
// odczyt parametrów ze sciezki
$php_self = $_SERVER['PHP_SELF'];
$script_name = $_SERVER['REQUEST_URI'];
$phppos = strpos($php_self, '.php');
$param = trim(substr($php_self, $phppos + 4));
if ($phppos > 0 && !$param) {
    $param = $_SERVER['REQUEST_URI'];
}
if ('/' == substr($param, 0, 1)) {
    if (strlen($param) > 1) {
        $param = substr($param, 1);
    } else {
        $param = '';
    }
}
list($param) = explode('?', $param);
$param = preg_replace("/\/+/", '/', $param);

$xLang = 'pl';
$exReqest = explode('/', $param);
if (isset($exReqest[0]) && strlen($exReqest[0]) == 2) {
    $xLang = $exReqest[0];
    array_shift($exReqest);
    $exReqest = array_values($exReqest);
}
qLayout::setting('subway', qConfig::get('path.subway', ''));
qCtrl::initParam();
qCtrl::init($param);
$oryginal = qCtrl::oryginalNoLang();
if ('index' == $oryginal || 'index/index' == $oryginal || 'index/home' == $oryginal) {
    qCtrl::location('');
}
if ('index' == qCtrl::module() && 'index' == qCtrl::action()) {
    //qLayout::meta('home_title', $homeTitle);
    //qLayout::meta('description', $homeDescription);

    qCtrl::init('index/index');
}
else {

}

$oryginal = qCtrl::oryginal();
if ('index/home' == $oryginal) {
    $oryginal = '';
}
qLayout::link('canonical', false, qHref::url($oryginal));
qCtrl::redir(false);

while (true) {
    $control = new qController();
    $result = $control->action();
    if ($result === false) {
        qCtrl::init('index/no/action/'.qCtrl::oryginal());
        qCtrl::redir(true);
    }
    if (qCtrl::redir()) {
        qCtrl::redir(false);
        continue;
    } 
    else {
        echo qLayout::render();
        exit;
    }
}
