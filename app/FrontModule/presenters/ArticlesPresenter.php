<?php

namespace FrontModule;

class ArticlesPresenter extends BasePresenter {

    public function renderDefault($section) {
        $articlesModel = $this->context->createArticles();
        $articles = $articlesModel->listSection($section);
        if (count($articles)) {
            $this->template->articles = $articles;
        } else {
            $this->flashMessage("V sekci není žádný článek.");
        }
        
        $navigationModel = $this->context->createNavigation();
        $this->template->section = $navigationModel->getSection($section);
    }

    public function renderDetail($section, $seoname) {
        $articlesModel = $this->context->createArticles();
        $this->template->article = $articlesModel->getArticle($section, $seoname);
    }

}