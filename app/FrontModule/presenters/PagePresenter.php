<?php

namespace FrontModule;

class PagePresenter extends BasePresenter {

    public function renderDefault($section, $seoname) {
        $pageModel = $this->context->createPage();
        $page = $pageModel->find($section, $seoname);
        $this->template->page = $page;
    }

}