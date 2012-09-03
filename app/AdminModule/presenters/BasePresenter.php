<?php

namespace AdminModule;

use Indil,
    \Nette\Security\User;

abstract class BasePresenter extends \BasePresenter {
    
    /** @persistent */
    public $lang;

    public function startup() {
        parent::startup();
        
        // user authentication
        if (!$this->user->isLoggedIn()) {
            if ($this->user->logoutReason === User::INACTIVITY) {
                $this->flashMessage('Byli jste odhlášni z důvodu dlouhodobé neaktivity. Přihlaste se znovu, prosím.');
            }
            $backlink = $this->application->storeRequest();
            $this->redirect('Sign:in', array('backlink' => $backlink));
        }
    }
    
    protected function createComponentNavigationCS($name) {
        $this->buildNavigation($name, 'cs');
	}
    
    protected function createComponentNavigationEN($name) {
        $this->buildNavigation($name, 'en');
	}
    
    private function buildNavigation($name, $lang) {
        $navigationModel = $this->context->createNavigation();
        $structure = $navigationModel->getStructure($lang);
        
        $nav = new Indil\Navigation($this, $name);
        
        foreach ($structure as $id => $item) {
            // build params
            $params = array();
            if ($item->section) {
                $params['section'] = $item->section;
            }
            if ($item->seoname) {
                $params['seoname'] = $item->seoname;
            }
            // create link
            if (!empty($item->admin_presenter)) {
                $link = $this->link($item->admin_presenter .':', $params);
            } else {
                $link = $this->link($item->presenter .':', $params);
            }
            
            // add item
            if ($item->parent_id === -1) { // is HP
                $navItem = $nav->setupHomepage($item->title, $link);
            } else {
                $parent = $this->getNode($nav, $item->parent_id);
                if ($parent) {
                    $navItem = $parent->add($item->title, $link);
                }
            }
            
            if ($this->getPresenter()->getLastCreatedRequestFlag('current')) {
//                if ($item->parent_id) {
//                    $parent = $this->getNode($nav, $item->parent_id);
//                    $nav->setCurrent($parent);
//                }
                $nav->setCurrent($navItem);
            }
        }
    }
    
    private function getNode($nav, $id) {
        foreach ($nav->getComponents(TRUE) as $control) {
            if ($control->name == $id) {
                return $control;
            }
        }
        return NULL;
    }
    
    public function dateToDB($date) {
        $d = \DateTime::createFromFormat('d. m. Y', $date);
        return $d->format('Y-m-d');
    }
    
    public function datetimeToDB($date) {
        $d = \DateTime::createFromFormat('d. m. Y H:i', $date);
        return $d->format('Y-m-d H:i');
    }
}