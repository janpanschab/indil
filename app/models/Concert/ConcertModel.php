<?php

class ConcertModel extends Model {
    
    public function listPlanned() {
        return $this->database->table(self::CONCERT)->where('date >= CURDATE()')->order('date');
    }
    
    public function get($id) {
        return $this->database->table(self::CONCERT)->get($id);
    }
    
    public function update($id, $data) {
        $this->database->table(self::CONCERT)->get($id)->update($data);
    }
    
    public function insert($data) {
        $this->database->table(self::CONCERT)->insert($data);
    }
    
    public function delete($id) {
        $this->database->table(self::CONCERT)->where('id', $id)->delete();
    }
    
}