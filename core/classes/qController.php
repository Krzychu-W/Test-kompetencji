<?php

class qController extends qControllerAction {
    
    public function action($argsControl = null) {

        if ($argsControl === null) {
            $argsControl = qCtrl::allArgs();
        }
        // favicony
        $favicons = [
          'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png',
          'apple-touch-icon.png', 'android-chrome-192x192.png', 'android-chrome-512x512.png',
        ];
        if (in_array($argsControl[0], $favicons)) {
            $favicon = new qFavicon();
            $favicon->upload($argsControl[0]);
        }
        if (count($argsControl) > 0) {
            $module = $argsControl[0];
            if ($module == '') {
                return false;
            }
            $cName = 'q'.ucfirst($module).'Controller';
            if (qLoader::isClass($cName)) {
                $mControl = new $cName();
                $mControl->setFrom([$module]);
                $mControl->setArgs(array_slice($argsControl, 1));
                return $mControl->action();
            }
            return false;
        } 
        return false;
    }
}
