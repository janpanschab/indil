<?php

use Nette\Diagnostics\Debugger;

class ImageModel extends Model {
    
    public function createFolder($path) {
        if (!@mkdir($path)) {
            throw new Nette\IOException("Nepodařilo se vytvořit složku $path");
        }
    }
    
}