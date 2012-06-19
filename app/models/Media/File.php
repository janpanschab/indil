<?php

use Nette\Diagnostics\Debugger;

class File extends Nette\Object {
    
    /** @var int */
    public $id;
    
    /** @var string */
    public $ext;
    
    /** @var string */
    public $name;
    
    /** @var string */
    private $url;
    
    /** @var array */
    private $link;
    
    /** @var boolean */
    private $image;
    
    /** @var Nette\Database\Connection */
    private $database;
    
    private static $imageMimeType = array(
        'image/jpeg',
        'image/png',
        'image/gif'
    );

    public function __construct(Nette\Database\Connection $database, $id) {
        $this->database = $database;
        $this->id = (int)$id;
        
        try {
            $row = $this->getDbFile();
            $this->ext = $row->ext;
            $this->name = $row->name;
            
        } catch (\Nette\InvalidArgumentException $e) {
            throw new \Nette\InvalidArgumentException("Neplatný soubor id $this->id ...", NULL, $e);
        }
    }

     /**
     * Returns absolute URL
     * @return string
     */
    public function getUrl() {
        $path = $this->getPath($this->id);
        return Utils::getUrl($path);
    }
    
    /**
     * Returns array of links for each image type or url if type is specified
     * @param string
     * @return array|string|NULL
     */
    public function getLink($type = NULL) {
        if ($this->isImage()) {
            $config = \Nette\Utils\Neon::decode(file_get_contents(APP_DIR .'/config/image.neon'));
            if ($type) {
                return Utils::getUrl(MEDIA_DIR .'/'. $type .'/'. $this->id .'.'. $this->ext);
            }
            $link = array();
            foreach ($config as $type => $params) {
                $link[$type] = Utils::getUrl(MEDIA_DIR .'/'. $type .'/'. $this->id .'.'. $this->ext);
            }
            return $link;
        }
        return NULL;
    }
    
    /**
     * Is file type of image?
     * @return boolean
     */
    public function isImage() {
        $image = getimagesize($this->getPath());
        return in_array($image['mime'], self::$imageMimeType);
    }

    /**
     * Returns absolute path in filesystem
     * @return string
     */
    public function getPath() {
        return MEDIA_DIR .'/'. $this->id .'.'. $this->ext;
    }
    
    /**
     * Returns row form db representing the file
     * @return Nette\Database\Table\ActiveRow
     * @throws Nette\InvalidArgumentException
     */
    public function getDbFile() {
        $row = $this->database->table(Model::FILES)->get($this->id);
        if (!$row) {
            throw new \Nette\InvalidArgumentException("Soubor id $this->id se nepodařilo v tabulce ". Model::FILES ." najít.");
        }
        return $row;
    }
    
    /**
     * Delete file from db and from filesystem
     * @throws \Nette\IOException
     */
    public function delete() {
        // try first delete file from db in transaction and then from filesystem
        $this->database->beginTransaction();
        $row = $this->getDbFile();
        $row->delete();
        
        try {
            $this->deleteFile();
            $this->database->commit();
            
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw new \Nette\IOException("Soubor id $this->id nemohl být smazán, protože... ", NULL, $e);
        }
    }

    /**
     * Delete file from filesystem
     * @throws \Nette\IOException
     * @throws \Nette\FileNotFoundException
     */
    private function deleteFile() {
        $path = $this->getPath();
        if (is_file($path)) {
            if (!@unlink($path)) {
                throw new \Nette\IOException("Soubor $path se nepodařilo smazat.");
            }
        } else {
            throw new \Nette\FileNotFoundException("Nebyl nalezen soubor $path.");
        }
    }
}
