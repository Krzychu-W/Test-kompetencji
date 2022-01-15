<?php

/**
 * Skanowanie folderu
 */
class qFolderScan {

    public function scan($path, $type = 'ALL', $extension = false) {
        $items = [];
        if (is_dir($path)) {
            foreach (new DirectoryIterator($path) as $file) {
                $add = false;
                if ($type === 'ALL') {
                    $add = true;
                }
                else if ($type === 'SUBDIR' && ($file->isDir() && !$file->isDot())) {
                    $add = true;
                }
                else if ($type === 'FILE' && $file->isFile()) {
                    $add = true;
                }
                else if ($type === 'EXTENSION' && ($file->isFile() && $file->getExtension() === $extension)) {
                    $add = true;
                }
                if ($add) {
                    $item = new stdClass();
                    $item->getATime = $file->getATime();
                    $item->getCTime = $file->getCTime();
                    $item->getMTime = $file->getMTime();
                    $item->getBasename = $file->getBasename();
                    $item->getExtension = $file->getExtension();
                    $item->getFilename = $file->getFilename();
                    $item->getPath = $file->getPath();
                    $item->getPathname = $file->getPathname();
                    $item->getSize = $file->getSize();
                    $item->getType = $file->getType();
                    $item->isDir = $file->isDir();
                    $item->isDot = $file->isDot();
                    $item->isSubdir = ($item->isDir && !$item->isDot);
                    $item->isFile = $file->isFile();
                    $item->isLink = $file->isLink();
                    $item->isReadable = $file->isReadable();
                    $item->isWritable = $file->isWritable();
                    $items[] = $item;
                }
            }
        }
        return $items;
    }
    
    static function sort($items, $column, $dir = 'asc') {
        if (isset($items[0])) {
            
            if (is_string($items[0]->$column)) {
                if ($dir == 'asc') {
                    return qArraySort::ascByStringFieldObj($items, $column);
                }
                else {
                    return qArraySort::descByStringFieldObj($items, $column);
                }
            }
            if ($dir == 'asc') {
                return qArraySort::ascByIntFieldObj($items, $column);
            }
            else {
                return qArraySort::descByIntFieldObj($items, $column);
            }
        }
        return $items;
    }
    
    static function fileItems($path, $extension = false, $sort = false) {
        if ($extension) {
            $items = self::scan($path, 'EXTENSION', $extension);
        }
        else {
            $items = self::scan($path, 'FILE');
        }
        if ($sort) {
            $ex = explode('/', $sort);
            if (count($ex) == 1) {
                $ex[1] = 'asc';
            }
            if ($ex[0] == 'modify') {
                $ex[0] = 'getMTime';
            }
            else if ($ex[0] == 'filname') {
                $ex[0] = 'getFilename';
            }
            $items = self::sort($items, $ex[0], $ex[1]);
        }
        return $items;
    }
    
    static function fileNames($path, $extension = false, $sort = false) {
        $items = [];
        foreach (self::fileItems($path, $extension, $sort) as $item) {
            $items[] = $item->getFilename;
        }
        return $items;
    }
    
    static function subdirItems($path) {
        return self::scan($path, 'SUBDIR');
    }
    
    static function subdirNames($path) {
        $items = [];
        foreach (self::subdirItems($path) as $item) {
            $items[] = $item->getFilename;
        }
        return $items;
    }
    
    public static function phpItems($path) {
        return self::fileItems($path, 'php');
    }
    
    public static function phpNames($path) {
        $items = [];
        foreach (self::phpItems($path) as $item) {
            $items[] = $item->getFilename;
        }
        return $items;
    }
}

