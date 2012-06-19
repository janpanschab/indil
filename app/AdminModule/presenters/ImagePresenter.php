<?php

namespace AdminModule;

use Nette\Image;

class ImagePresenter extends BasePresenter {

    public function renderDefault($type, $filename, $ext) {
        $file = $filename .'.'. $ext;
        if (file_exists(MEDIA_DIR .'/'. $file)) {
            $folder = MEDIA_DIR .'/'. $type;
            \Nette\Diagnostics\Debugger::barDump(!file_exists($folder));
            if (!file_exists($folder)) {
                $imageModel = $this->context->createImage();
                $imageModel->createFolder($folder);
            }
            
            $config = \Nette\Utils\Neon::decode(file_get_contents(APP_DIR .'/config/image.neon'));
            
            $newImage = Image::fromFile(MEDIA_DIR .'/'. $file);
            $newImage->resize($config[$type]['width'], $config[$type]['height'], $config[$type]['method']);
            $newImage->save(MEDIA_DIR .'/'. $type .'/'. $file);
            $newImage->send();
            
            $this->terminate();
        } else {
            throw new \Nette\FileNotFoundException("Soubor ". MEDIA_DIR ."/$file neexistuje");
        }
    }
}