<?php

class qFavicon {
    
    public function upload($file) {
        $file = qConfig::get('path.root').DS.'volume'.DS.qConfig::get('volume')
            .DS.'image'.DS.'favicon'.DS.$file;
        if (file_exists($file)) {
            $mime = qMimeType::get($file);
            header('Content-Type: '.$mime);
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
            header('Pragma: public');
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-Length: '.filesize($file));
            readfile($file);
            exit;
        }
        qVisitor::add();
        exit;
    }
}