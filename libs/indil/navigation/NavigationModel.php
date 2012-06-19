<?php

class NavigationModel extends Model {
    
    public function getStructure($lang) {
        $structure = array();
        foreach ($this->database->table(self::NAVIGATION)->where('lang', $lang) as $item) {
            $structure[] = $item;
        }
        return $structure;
    }
    
    public function getStructureSeoname($id) {
        $structure = $this->database->table(self::NAVIGATION)->get($id);
        if ($structure) {
            return $structure->seoname;
        }
    }
    
}