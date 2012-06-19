<?php

use Nette\Diagnostics\Debugger;

class MediaModel extends Model {
    
    /**
     * List all files in folder
     * @param int $folder
     * @return array
     */
    public function getFiles($folder) {
        $files = array();
        foreach ($this->database->table(self::FILES)->where('folder', $folder) as $file) {
            $files[] = $this->context->createFile($file->id);
        }
        return $files;
    }
    
    /**
     * Rename file
     * @param type $id
     * @param type $newname
     */
    public function renameFile($id, $newname) {
        $this->database->table(self::FILES)->get($id)->update(array('name' => $newname));
    }
    
    /**
     * List all folders in parent folder
     * @param int $parent
     * @return array
     */
    public function getFolders($parent) {
        $folders = array();
        foreach ($this->database->table(self::FOLDERS)->where('parent', $parent)->where('id != 0') as $folder) {
            $folders[] = $this->context->createFolder($folder->id);
        }
        return $folders;
    }
    
    /**
     * List all folders
     * @return array
     */
    public function getAllFolders() {
        $folders = array();
        foreach ($this->database->table(self::FOLDERS)->where('id != 0') as $folder) {
            $folders[] = $this->context->createFolder($folder->id);
        }
        return $folders;
    }

    /**
     * Rename folder
     * @param type $id
     * @param type $newname
     */
    public function renameFolder($id, $newname) {
        $this->database->table(self::FOLDERS)->get($id)->update(array('name' => $newname));
    }
    
    /**
     * Create new folder in parent folder
     * @param string $name
     * @param int $parent
     */
    public function addFolder($name, $parent) {
        $this->database->table(self::FOLDERS)->insert(array(
            'parent' => $parent,
            'name' => $name,
        ));
    }
    
    /**
     * Returns array of folder parents or FALSE
     * @param int $id
     * @return array|FALSE
     */
    public function getCrumbs($id) {
        if ($id !== 0) { // folder id 0 is virtual
            $folder = $this->context->createFolder($id);
            if ($folder->parent !== FALSE) { // $folder->parent could be zero, which is also FALSE
                $crumbs = array($folder->name);
                while ($folder->parent !== 0) {
                    $folder = $this->context->createFolder($folder->parent);
                    array_push($crumbs, $folder->name);
                }
                return array_reverse($crumbs);
            }
        }
        return FALSE;
    }
    
    /**
     * Returns id of parent folder
     * @param int $id
     * @return Folder
     * @throws \Nette\InvalidArgumentException
     */
    public function getParentFolder($id) {
        if ($id === 0) {
            return FALSE;
        }
        return $this->context->createFolder($id);
    }
    
    public function upload($folder) {
        $allowedExtensions = array('pdf', 'zip', 'doc', 'xls', 'docx', 'xlsx', 'mp3', 'ogg', 'jpg', 'gif', 'png');
        // max file size in bytes
        $sizeLimit = 30 * 1024 * 1024;
        
        $this->database->beginTransaction();
        $nextId = $this->getNextId(); // use last inserted id
        Debugger::fireLog($nextId);
        
        $uploader = new \qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload(MEDIA_DIR, TRUE, $nextId);
        
        if (isset($result['success']) && $result['success']) {
            $this->database->table(Model::FILES)->insert(array(
                'id' => $nextId,
                'folder' => $folder,
                'name' => $result['name'],
                'ext' => $result['ext']
            ));
            $this->database->commit();
        } else {
            $this->database->rollBack();
        }
        
        // to pass data through iframe you will need to encode all html tags
        return htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    /**
     * Returns next id from db which will be inserted
     * @return int
     */
    public function getNextId() {
        $result = $this->database->query('SHOW TABLE STATUS LIKE "'. self::FILES .'"')->fetch();
        return $result['Auto_increment'];
    }

}