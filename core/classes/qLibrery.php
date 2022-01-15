<?php

class qLibrery {
    
    public function hrefCssMerge($files){
        $time = 0;
        foreach ($files as $file) {
            $pf = $this->pathCssFile($file);
            $fTime = filemtime($pf);
            if ($fTime > $time) {
                $time = $fTime;
            }
        }
        $generate = true;
        $fileMinifyCss = $this->pathMergeCss();
        $fileMinifyTime = 0;
        if (file_exists($fileMinifyCss)) {
            $fileMinifyTime = filemtime($fileMinifyCss);
            if ($fileMinifyTime > $time) {
                $generate = false;
            }
        }
        $subPath = 'volume'.DS.qConfig::get('volume').DS.'theme'.DS.'cssMinify';
        $mCssFile = 'mstyle.css';
        if ($generate) {
            $result = '';
            foreach ($files as $file) {
                $result .= $this->minifyCss($file);
            }
            qPath::checkFolder($subPath, 0755);
            $fp = fopen($fileMinifyCss, 'w');
            flock($fp, 2);
            fwrite($fp, $result);
            flock($fp, 3);
            fclose($fp);
            $fileMinifyTime = filemtime($fileMinifyCss);
        }
        return qHref::link(str_replace('\\', '/', $subPath.DS.$mCssFile)).$this->addTimeByTime($fileMinifyTime);
    }
    
    public function hrefCssFile($href) {
        if (qString::strBegin($href, 'volume://')) {
            $path = qLayout::pathThemeVolume().'/css/'.qString::cutBegin($href, 'volume://');
            if (file_exists($path)) {
                return qLayout::urlThemeVolume().'/css/'.qString::cutBegin($href, 'volume://').$this->addTimeByPath($path);
            }
            return false;
        }
        elseif (qString::strBegin($href, 'core://')) {
            $path = qLayout::pathThemeCore().'/css/'.qString::cutBegin($href, 'core://');
            if (file_exists($path)) {
                return qLayout::urlThemeCore().'/css/'.qString::cutBegin($href, 'core://').$this->addTimeByPath($path);
            }
            return false;
        } 
        return $href;
    }

    public function hrefJsMerge($files){
        $time = 0;
        foreach ($files as $file) {
            $pf = $this->pathJsFile($file);
            $fTime = filemtime($pf);
            if ($fTime > $time) {
                $time = $fTime;
            }
        }
        $generate = true;
        $fileMinifyJs = $this->pathMergeJs();
        $fileMinifyTime = 0;
        if (file_exists($fileMinifyJs)) {
            $fileMinifyTime = filemtime($fileMinifyJs);
            if ($fileMinifyTime > $time) {
                $generate = false;
            }
        }
        $subPath = 'volume'.DS.qConfig::get('volume').DS.'theme'.DS.'jsMinify';
        $mJsFile = 'script.js';
        if ($generate) {
            $result = '';
            foreach ($files as $file) {
                $result .= $this->minifyJs($file);
            }
            qPath::checkFolder($subPath, 0755);
            $fp = fopen($fileMinifyJs, 'w');
            flock($fp, 2);
            fwrite($fp, $result);
            flock($fp, 3);
            fclose($fp);
            $fileMinifyTime = filemtime($fileMinifyJs);
        }
        return qHref::link(str_replace('\\', '/', $subPath.DS.$mJsFile)).$this->addTimeByTime($fileMinifyTime);
    }
    
    public function hrefJsFile($href) {
        if (qString::strBegin($href, 'volume://')) {
            $path = qLayout::pathThemeVolume().'/javascript/'.qString::cutBegin($href, 'volume://');
            if (file_exists($path)) {
                $href = qLayout::urlThemeVolume().'/javascript/'.qString::cutBegin($href, 'volume://').$this->addTimeByPath($path);
            }
            else {
                return false;
            }
        }
        else if (qString::strBegin($href, 'core://')) {
            $path = qLayout::pathThemeCore().'/javascript/'.qString::cutBegin($href, 'core://');
            if (file_exists($path)) {
                $href = qLayout::urlThemeCore().'/javascript/'.qString::cutBegin($href, 'core://').$this->addTimeByPath($path);
            }
            else {
                return false;
            }
        } 
        if (PHP_OS == 'WINNT') {
            $href = str_replace('\\', '/', $href);
        }
        return $href;
    }
    
    public function contentScript($setting, $scripts) {
        $result = "<script>\n";
        $result .= "qAnt.setting=".json_encode($setting).";\n";
        foreach ($scripts as $item) {
            $result .= $item."\n";
        }
        $result .= "</script>\n";
        return $result;
    }

    private function minifyCss($file) {
        $str = file_get_contents($this->pathCssFile($file));

        // --- wstępna obróbka
        $str = "\n".$str."\n";
        $str = str_replace("\r", "", $str);
        // --- /wstępna obróbka

        // --- /usuń komentarze
        $str = preg_replace('/\/\*.*?\*\//s', '', $str); // /*x*/
        // --- /usuń komentarze

        // --- usuń spacje i tabulatory
        $str = preg_replace('/\n[ \t\f]+/', "\n", $str); // |_
        $str = preg_replace('/[ \t\f]+\n/', "\n", $str); // _|

        $str = preg_replace('/[ \t\f]*:[ \t\f]*/', ':', $str);   // _:_ 
        $str = preg_replace('/[ \t\f]*;[ \t\f]*/', ';', $str);   // _;_ 
        $str = preg_replace('/[ \t\f]*,[ \t\f]*/', ",", $str);   // _,_

        $str = preg_replace('/[ \t\f]*\>[ \t\f]*/', '>', $str); // _>_
        $str = preg_replace('/[ \t\f]*\+[ \t\f]*/', '+', $str); // _+_
        $str = preg_replace('/[ \t\f]*\~[ \t\f]*/', '~', $str); // _~_

        $str = preg_replace('/[ \t\f]*\{[ \t\f]*/', '{', $str); // _{_
        $str = preg_replace('/[ \t\f]*\}[ \t\f]*/', '}', $str); // _}_

        $str = preg_replace('/[ \t\f]*\([ \t\f]*/', '(', $str); // _(_
        $str = preg_replace('/[ \t\f]*\)[ \t\f]*/', ')', $str); // _)_
        // --- /usuń spacje i tabulatory

        /* --- przywróć spacje w calc() --- */
        $str = str_replace("%-", '% - ', $str);
        $str = str_replace("%+", '% + ', $str);
        /* --- /przywróć spacje w calc() --- */

        // --- dodaj spacje dla @supports
        $str = str_replace('@supports(',     '@supports (', $str);
        $str = str_replace('@supports not(', '@supports not (', $str);
        $str = str_replace(')and(',          ') and (', $str);  
        $str = str_replace(')or(',           ') or (', $str);
        $str = str_replace('(not(',          '( not (', $str);
        // --- /dodaj spacje dla @supports

        // --- usuń wartości zerowe
        $str = str_replace(":0px;", ':0;', $str);
        $str = str_replace(":0em;", ':0;', $str);
        $str = str_replace(":0ex;", ':0;', $str);
        $str = str_replace(":0%;",  ':0;', $str);
        // --- /usuń wartości zerowe

        // --- usuń puste klasy
        $str = preg_replace('/\n.+\{\n\}/', '', $str); // |x{|}
        // --- /usuń puste klasy

        // --- zmień przełamania wierszy
        $str = preg_replace('/[\n]+/', '', $str); // usuń przełamania wiersza
        $str = str_replace("}", "}\n", $str);     // dodaj przełamania wiersza po "}"
        $str = str_replace("\n}", "}", $str);     // usuń przełamanie przed "\n}"
        $str = preg_replace("/^\n/", "", $str);   // usuń pierwszą pustą linię  
        $str = preg_replace("/\n$/", "", $str);   // usuń ostatnią pustą linię
        // --- /zmień przełamania wierszy  

        return $str;

    }
    
    private function pathCssFile($file) {
        if (qString::strBegin($file, 'core://')) {
            return qConfig::get('path.root').DS.'core'.DS.'theme'.DS.'css'.DS
                .qString::cutBegin($file, 'core://');
        }
        if (qString::strBegin($file, 'volume://')) {
            return qConfig::get('path.root').DS.'valume'.DS.'theme'
                .qConfig::get('volume').DS.'theme'.DS.'css'.DS
                .qString::cutBegin($file, 'volume://');
        }
        // awaryjnie dla debugowania błędów
        return $file;
    }
    
    private function pathMergeCss() {
       return qConfig::get('path.root').DS.'volume'.DS
                .qConfig::get('volume').DS.'theme'.DS.'cssMinify'.DS.'mstyle.css';   
    }

    
    private function minifyJs($file) {
        $str = file_get_contents($this->pathJsFile($file));
        $obj = new qLibreryJs();
        $mini = $obj->minify($str);
        if ($mini) {
            return $mini;
        }
        return $str;
    }
    
    private function pathJsFile($file) {
        if (qString::strBegin($file, 'core://')) {
            return qConfig::get('path.root').DS.'core'.DS.'theme'.DS.'javascript'.DS
                .qString::cutBegin($file, 'core://');
        }
        if (qString::strBegin($file, 'volume://')) {
            return qConfig::get('path.root').DS.'valume'.DS.'theme'
                .qConfig::get('volume').DS.'theme'.DS.'javascript'.DS
                .qString::cutBegin($file, 'volume://');
        }
        // awaryjnie dla debugowania błędów
        return $file;
    }
    
    private function pathMergeJs() {
       return qConfig::get('path.root').DS.'volume'.DS
                .qConfig::get('volume').DS.'theme'.DS.'jsMinify'.DS.'script.js';   
    }
    
    private function addTimeByTime($time) {
        return '?'.base_convert($time, 10, 36);
    }
    
    private function addTimeByPath($path) {
        return $this->addTimeByTime(filemtime($path));
    }
}
