<?php

class ArticlesModel extends Model {

    public function listSection($section) {
        return $this->database->table(self::ARTICLES)->where('section', $section)->order('date DESC');
    }

    public function getArticle($section, $seoname) {
        return $this->database->table(self::ARTICLES)->where('section', $section)->where('seoname', $seoname)->fetch();
    }
    
    public function get($id) {
        return $this->database->table(self::ARTICLES)->get($id);
    }
    
    public function update($id, $data) {
        $this->database->table(self::ARTICLES)->get($id)->update($data);
    }
    
    public function insert($data) {
        $this->database->table(self::ARTICLES)->insert($data);
    }
    
    public function delete($id) {
        $this->database->table(self::ARTICLES)->where('id', $id)->delete();
    }

}