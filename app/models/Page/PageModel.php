<?php

class PageModel extends Model {
    
    /**
     * Returns page from db
     * @param int $id
     * @return \Nette\Database\Row
     */
    public function get($id) {
        return $this->database->table(Model::PAGE)->get($id);
    }


    /**
     * Returns page in specified structure with specified seoname
     * @param string $section
     * @param string $seoname
     * @return \Nette\Database\Row
     * @throws InvalidArgumentException
     */
    public function find($section, $seoname) {
        try {
            $id = $this->findId($section, $seoname);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException("Stránka neexistuje...", NULL, $e);
        }
        
        return $this->get($id);
    }

    /**
     * Find id of page in specified structure with specified seoname
     * @param int $section
     * @param string $seoname
     * @return int
     * @throws InvalidArgumentException
     */
    public function findId($section, $seoname) {
        if (isset($section) && isset($seoname)) {
            $item = $this->database->table(self::PAGE)->where('seoname', $seoname)->where('section', $section)->fetch();
        } else if (isset($section)) {
            $item = $this->database->table(self::PAGE)->where('section', $section)->fetch();
        } else if (isset($seoname)) {
            $item = $this->database->table(self::PAGE)->where('seoname', $seoname)->fetch();
        } else {
            throw new InvalidArgumentException("Stránka není ani v sekci ani nemá seoname");
        }
        
        if ($item) {
            return $item->id;
        } else {
            throw new InvalidArgumentException("Stránka v sekci '$section' a seoname '$seoname'");
        }
    }
    
    /**
     * Returns 1 if is page inserted, otherwise 0
     * @param type $id
     * @return int
     */
    public function isInsertedId($id) {
        return count($this->database->table(self::PAGE)->get($id));
    }
    
    /**
     * Update current page
     * @param array $data 
     */
    public function update($id, $data) {
        $this->database->table(self::PAGE)->get($id)->update($data);
    }

}