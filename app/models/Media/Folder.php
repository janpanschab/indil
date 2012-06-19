<?php

use Nette\Diagnostics\Debugger;

/**
 * Description of Folder
 *
 * @author Hona
 */
class Folder extends Nette\Object {
    
    /** @var int */
    public $id;
    
    /** @var string */
    public $name;
    
    /** @var int */
    public $parent;
    
    /** @var array */
    private $tree;
    
    /** @var string */
    private $path;
    
    /** @var Nette\Database\Connection */
    private $database;

    public function __construct(Nette\Database\Connection $database, $id) {
        $this->database = $database;
        $this->id = (int)$id;
        
        try {
            $row = $this->getDbFolder();
            $this->name = $row->name;
            $this->parent = $row->parent;
            
        } catch (\Nette\InvalidArgumentException $e) {
            throw new \Nette\InvalidArgumentException("Neplatná složka id $this->id ...", NULL, $e);
        }
    }
    
    /**
     * Return tree of folder as array($folder->id => $folder->name)
     * @return array
     */
    public function getTree() {
        $tree = array($this->id => $this->name);
        $parent = $this->parent;
        
        while ($parent !== 0) {
            $folder = new self($this->database, $parent);
            $tree[$folder->id] = $folder->name;
            $parent = $folder->parent;
        }
        
        return array_reverse($tree);
    }
    
    
    public function getPath() {
        $tree = $this->getTree();
        $path = implode('/', $tree);
        return '/'. $path;
    }

    /**
     * Returns row from db representing the folder
     * @return Nette\Database\Table\ActiveRow
     * @throws Nette\InvalidArgumentException
     */
    public function getDbFolder() {
        $row = $this->database->table(Model::FOLDERS)->get($this->id);
        if (!$row) {
            throw new \Nette\InvalidArgumentException("Složka id $this->id se nepodařilo v tabulce ". Model::FOLDERS ." najít.");
        }
        return $row;
    }
    
    /**
     * Delete folder from db
     */
    public function delete() {
        $row = $this->getDbFolder();
        try {
            $row->delete();
        } catch (PDOException $e) {
            switch ($e->getCode()) {
                case 23000: $message = 'Složka nemůže být smazána, protože není prázdná.'; break;
                default: 'Složku se nepodařilo smazat ('. $e->getMessage() .').';
            }
            throw new Indil\MediaException($message, NULL, $e);
        }
    }
}
