<?php

class Model extends Nette\Object {
    
    const FOLDERS = 'folders',
            FILES = 'files',
            NAVIGATION = 'navigation',
            PAGE = 'page',
            ARTICLES = 'articles',
            CONCERT = 'concerts';
    
    /** @var Nette\Database\Connection */
    public $database;
    
    /** @var Nette\DI\IContainer */
    public $context;

    public function __construct(Nette\Database\Connection $database, Nette\DI\IContainer $context) {
        $this->database = $database;
        $this->context = $context;
    }

    /** @return Authenticator */
    public function createAuthenticatorService() {
        return new Authenticator($this->database->table('users'));
    }

}
