<?php

class qInstall {

    /**
     * Licznik błędów.
     *
     * @var int
     */
    protected $failCount = 0;

    /**
     * Regex używany do wklejania informacji w ścieżce pliku.
     *
     * @var string
     */
    protected $regex = '{{ {0,}([a-zA-Z0-9$#:_-]+) {0,}}}';

    /**
     * Tablica informacji o ścieżkach.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Install constructor
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Funkcja opisująca co instalator ma sprawdzić itd.
     */
    protected function init() {
        // Permissions    
        $pathVol = qConfig::get('path.volume');
        $this->permission($pathVol, 0755);
        $this->permission($pathVol.DS.'data', 0755);
        $this->permission($pathVol.DS.'file', 0755);
        $this->permission($pathVol.DS.'image', 0755);
        $this->permission($pathVol.DS.'thumb', 0755);
        $this->permission($pathVol.DS.'private', 0755);
        $this->permission($pathVol.DS.'archive', 0755);
        
        
        $this->htaccess($pathVol.DS.'data');
        $this->htaccess($pathVol.DS.'private');
        $this->htaccess($pathVol.DS.'archive');
        $this->htaccess(qConfig::get('path.base').DS.'config');
        $this->htaccess(qConfig::get('path.base').DS.'core');
        $this->htaccess(qConfig::get('path.base').DS.'core'.DS.'theme');
        $this->htaccess(qConfig::get('path.base').DS.'core'.DS.'theme'.DS.'css', true);
        $this->htaccess(qConfig::get('path.base').DS.'core'.DS.'theme'.DS.'javascript', true);

        $pathLog = qConfig::get('path.base').DS.'log';
        $this->permission($pathLog, 0755);
        $this->htaccess(qConfig::get('path.base').DS.'log');
        // SSL file
        if (qConfig::get('domain.sslfile')) {
            $this->permission(qConfig::get('domain.sslfile'), 0777, true, true);
        }
        
        // Softwares
        if (version_compare(PHP_VERSION, '7.0.32') < 0) {
            ++$this->failCount;
        }   
    }
    
    protected function htaccess($path, $access = false) {
        $file = $path.DS.'.htaccess';
        if (!file_exists($file)) {
            $fp = fopen($file, 'a');
            flock($fp, 2);
            if ($access) {
                fwrite($fp, "order allow,deny\nAllow from all  from all");
            }
            else {
                fwrite($fp, "order allow,deny\n  deny from all\n");
            }
            flock($fp, 3);
            fclose($fp);
        }
        $this->permission($file, 0644, true, false);
    }

    /**
     * Funkcja sprawdzająca uprawnienia.
     *
     * @param $path
     * @param $chmod
     * @param $file
     */
    protected function permission($path, $chmod, $file = false, $tryCreate = true) {
        
        // omiń duplikaty
        foreach ($this->permissions as $permission) {
            if ($permission['path'] == $path) {
                return;
            }
        }
        // sprawdź
        $error = false;
        if (!file_exists($path)) {
            // załóż plik/katalog
            if (true == $file) {
                @file_put_contents($path, '');
            } else {
                @mkdir($path, $chmod, true);
            }
        }
        if (file_exists($path)) {
            // kontrola uprawnień
            $fileChmod = substr(sprintf('%o', fileperms($path)), -3);
            $value = $fileChmod;
            if ($fileChmod != sprintf('%o', $chmod) && Environment::OS_WIN != Environment::getOS()) {
                if (@chmod($path, $chmod) === false) {
                    $value .= ' | Niewłaściwe uprawnienia: '.$fileChmod.'.';
                    $error = true;
                }
            }
        } else {
            $value = 'Nie istnieje!';
            $error = true;
        }
        $this->permissions[] = [
            'path' => $path,
            'correct' => sprintf('%o', $chmod),
            'value' => $value,
            'error' => $error,
          ];

        if (true == $error) {
            ++$this->failCount;
        }
    }

    /**
     * Funkcja określająca czy wystąpiły jakieś błędy.
     *
     * @return bool
     */
    public function check() {
        if ($this->failCount > 0) {
            return false;
        }
        return true;
    }

    /**
     * Generuje HTML zawierający tabelę z ścieżkami i uprawnieniami.
     *
     * @return string
     */
    protected function getPermissions() {
        $permissionsFiles = '';
        foreach ($this->permissions as $file) {
            if (true == $file['error']) {
                $permissionsFiles .= '<tr class="table-odd danger">';
            } else {
                $permissionsFiles .= '<tr class="table-odd">';
            }

            $permissionsFiles .= '<td class="column1">'.$file['path'].'</td>';
            $permissionsFiles .= '<td class="column2">'.$file['correct'].'</td>';
            $permissionsFiles .= '<td class="column3">'.$file['value'].'</td>';

            $permissionsFiles .= '</tr>';
        }

        return $permissionsFiles;
    }

    /**
     * Generuje HTML zawiarający tabelę z programami.
     *
     * @return string
     */
    protected function getSoftwares()
    {
        $softwares = '';

        // php
        if (version_compare(PHP_VERSION, '7.0.32') < 0) {
            $softwares .= '<tr class="table-odd danger">';
        } else {
            $softwares .= '<tr class="table-odd">';
        }

        $softwares .= '<td class="column1">PHP</td>';
        $softwares .= '<td class="column2">7.0.32</td>';
        $softwares .= '<td class="column3">'.PHP_VERSION.'</td>';
        $softwares .= '</tr>';
        // end php

        return $softwares;
    }

    /**
     * Funkcja zwracająca HTML instalatora.
     *
     * @return string
     */
    public function html()
    {
        $icon = qHref::link('favicon.ico');
        $logo = qHref::link('core/theme/image/logo.png');

        // return html
        return <<<PHP
<!DOCTYPE html>
<html>
<head>
    <title>Clouder - Instalacja</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="{$icon}"/>
    <style>
        body {
            background: #ecf0f1;
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        #container {
            min-height: 100vh;
            width: 100%;
            box-sizing: border-box;
            text-align: left;
            display: inline-block;
            max-width: 1180px;
            margin: 0px auto;
            background: #fff;
            padding: 30px 100px 100px 100px;
        }

        #header {
            padding-bottom: 30px;
        }

        h1 {
            margin: 0 auto;
            padding: 10px 0 20px;
            font-size: 30px;
            color: #3c3c3c;
        }

        table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
            margin: 0 auto;
            border: 1px solid #ccc;
            background: #ecf0f1;
            margin-bottom: 30px;
        }

        .btn {
            float: right;
            box-shadow: none;
            text-shadow: none;
            border: none;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -ms-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            background: #2c3e50;
            color: #fff;
            padding: 7px 17px 7px 17px;
            font-family: 'Arial', sans-serif;
            font-weight: 500;
            line-height: 1.3;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #354b60;
        }

        tbody tr:nth-child(2n+1) {
            background-color: #fff;
        }

        th {
            color: #999;
            padding: 10px 10px 20px;
            border-right: 1px dotted #ccc;
        }

        td {
            color: #3c3c3c;
            padding: 5px 10px;
            border-right: 1px dotted #ccc;
        }

        tr.danger td {
            color: #a94442 !important;
            background: #f2dede !important;
        }

        td:last-child, th:last-child {
            border-right: 0;
        }
    </style>
</head>
<body>
<div id="container">

    <div id="header"><img src="{$logo}" /></div>

    <h1>1. Uprawnienia plików i folderów</h1>
    <table class="table-order table table-condensed  table-ajax">
        <thead>
            <tr>
                <th class="column1">Ścieżka</th>
                <th class="column2">Wymagane uprawnienie</th>
                <th class="column3">Obecne ustawienie</th>
            </tr>
        </thead>
        <tbody class="table-content">
            {$this->getPermissions()}
        </tbody>
    </table>
    
    <h1>2. Oprogramowanie</h1>
    <table class="table-order table table-condensed  table-ajax">
        <thead>
            <tr>
                <th class="column1">Aplikacja</th>
                <th class="column2">Wymagane</th>
                <th class="column3">Zainstalowane</th>
            </tr>
        </thead>
        <tbody class="table-content">
            {$this->getSoftwares()}
        </tbody>
    </table>

    <input type="button" value="Odśwież" class="btn btn-info" id="refresh" onclick="window.location.reload();">
</div>
</body>
</html>
PHP;
    }
    
    public function folder($folder) {
        $this->permission($folder, 0755);
        $this->htaccess($folder);
    }
}
