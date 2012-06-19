<?php

namespace FrontModule;

use Indil;

abstract class BasePresenter extends \BasePresenter {
    
    /** @persistent */
    public $lang;

    public function createTemplate($class = NULL) {
        $template = parent::createTemplate($class);
        
        if (!isset($this->lang)) {
            $this->lang = $this->getHttpRequest()->detectLanguage(array('cs', 'en'));
        }
        if (!isset($this->lang)) {
            $this->lang = $this->context->parameters['lang'];
        }
        
        $this->context->translator->setLang($this->lang);
        $template->setTranslator($this->context->translator);

        return $template;
    }
    
    protected function beforeRender() {
        $this->template->lang = $this->lang;
    }


    protected function createComponentNavigation($name) {
        $navigationModel = $this->context->createNavigation();
        $structure = $navigationModel->getStructure($this->lang);
        
        $nav = new Indil\Navigation($this, $name);
        
        foreach ($structure as $id => $item) {
            if ($item->hidden) {
                continue;
            }
            // build params
            $params = array();
            if ($item->section) {
                $params['section'] = $item->section;
            }
            if ($item->seoname) {
                $params['seoname'] = $item->seoname;
            }
            
            // add item
            if ($item->parent_id === -1) { // is HP
                $navItem = $nav->setupHomepage($item->title, $this->link($item->presenter .':'));
            } else {
                $parent = $this->getNode($nav, $item->parent_id);
                if ($parent) {
                    $navItem = $parent->add($item->title, $this->link($item->presenter .':', $params));
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

}