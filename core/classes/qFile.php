<?php

class qFile {
    
    public function download($file_path, $download = false) {
        $mime = qMimeType::get($file_path);
        if ($download) {
            header("Content-disposition: attachment; filename={$download}");
        }
        header('Content-Type: '.$mime);
        $file = @fopen($file_path, 'rb');
        if ($file) {
            while (!feof($file)) {
                echo fread($file, 1024 * 8);
                flush();
                if (0 != connection_status()) {
                    @fclose($file);
                }
            }
            @fclose($file);
        }
        exit;
    }
    
    public static function getSizePrefix($size) {
        $ext = array('bajtów', 'KB', 'MB', 'GB');
        for ($i = 0; $i < 4; ++$i) {
            if ($size >= 1024) {
                $size /= 1024;
            } else {
                break;
            }
        }

        return number_format($size, 0, ',', '.').' '.$ext[$i];
    }
}

