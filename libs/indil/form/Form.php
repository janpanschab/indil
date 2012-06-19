<?php

namespace Indil;

use Nette\Utils\Html;

class Form extends \Nette\Application\UI\Form {

    public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
        parent::__construct($parent, $name);
        
        $this->setCustomRenderer();
    }

    public function setCustomRenderer() {
        $renderer = $this->getRenderer();
        
        $renderer->wrappers['form']['container'] = NULL;
        //$renderer->wrappers['error']['container'] = HTML::el('ul')->class('error');
        $renderer->wrappers['pair']['container'] = NULL; //Html::el('p')
        $renderer->wrappers['label']['container'] = NULL;
        $renderer->wrappers['label']['requiredsuffix'] = ' *';
        $renderer->wrappers['control']['container'] = NULL;
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['group']['container'] = NULL;

        $this->setRenderer($renderer);
    }

}