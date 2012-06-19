<?php

abstract class BasePresenter extends Nette\Application\UI\Presenter {
    
    protected function beforeRender() {
        // config admin defaults
        $this->template->indil = $this->context->parameters['indil'];
    }
    
    protected function createTemplate($class = NULL) {
        $template = parent::createTemplate($class);
        
        $texy = $this->context->getService('texy');
        $texy->encoding = 'utf-8';
        $texy->allowedTags = Texy::NONE;
        $texy->allowedStyles = Texy::NONE;
        $texy->setOutputMode(Texy::HTML5);
        # v případě HTML vypne odstranění volitelných koncových značek
        $texy->htmlOutputModule->removeOptional = false;
        # zalamování řádků pomocí enter
        $texy->mergeLines = false;
        # šířka tabulátorů kvůli převední na mezery
        $texy->tabWidth = 4;
        # maximální šířka řádku
        $texy->htmlOutputModule->lineWrap = 120;
        # odstranění zprávy <!-- by Texy2! -->
        //Texy::$advertisingNotice = false;
        # maskování emailové adresy před roboty
        //$texy->obfuscateEmail = false;
        # fráze
        $texy->allowed['phrase/ins'] = true;   // ++inserted++
        $texy->allowed['phrase/del'] = true;   // --deleted--
        $texy->allowed['phrase/sup'] = true;   // ^^superscript^^
        $texy->allowed['phrase/sub'] = true;   // __subscript__
        $texy->allowed['phrase/cite'] = true;   // ~~cite~~
        $texy->allowed['deprecated/codeswitch'] = true; // `=code
        # nadpisy
        $texy->headingModule->balancing = TEXY_HEADING_FIXED;
        # relativni odkazy (dokumenty)
        $texy->linkModule->root = '/_docs/';
        # obrázky
        $texy->imageModule->root = '/_images/';
        $texy->imageModule->linkedRoot = '/_images/';
        $texy->imageModule->fileRoot = '/_images/'; // rozmery obrazku
        // zarovnání obrázků pomocí třídy
        $texy->alignClasses['left'] = 'left';
        $texy->alignClasses['right'] = 'right';
        $texy->figureModule->widthDelta = 0;

        # moje handlery
        //$texy->addHandler('image', array($texy, 'imageHandler'));
        $texy->addHandler('script', array($texy, 'scriptHandler'));

        $template->registerHelper('texy', array($texy, 'process'));

//        $template->registerHelperLoader('Helpers::loader');

        return $template;
    }
}