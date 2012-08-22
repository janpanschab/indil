<?php

namespace AdminModule;

use Indil\Form,
    \Nette\Utils\Strings;

class ArticlesPresenter extends BasePresenter {

    public function renderDefault($section) {
        $articlesModel = $this->context->createArticles();
        $articles = $articlesModel->listSection($section);
        if (count($articles)) {
            $this->template->articles = $articles;
        } else {
            $this->flashMessage("V sekci není žádný článek.");
        }
    }

    public function actionEdit($id, $section) {
        $articlesModel = $this->context->createArticles();
        $article = $articlesModel->get($id);
        if (isset($article['date'])) {
            $article['date'] = $article['date']->format('d. m. Y');
        }
        if ($article) {
            $this['articleEdit']->setDefaults($article);
        }
        $this->template->form = $this['articleEdit'];
    }
    
    protected function createComponentArticleEdit() {
        $section = $this->getParam('section');
        $form = new Form;
        $form->getElementPrototype()->class('articles-add');
        $form->addText('title', 'Nadpis')
                ->addRule(Form::FILLED, 'Vyplňte nadpis článku');
        $form->addText('date', 'Datum')
                ->setDefaultValue(date('j. n. Y'))
                ->addRule(Form::FILLED, 'Vyplňte datum článku')
                ->getControlPrototype()->class('datum');
        $form->addText('description', 'Popis článku:')
                ->getControlPrototype()->class('longer')->maxLength(160);
        $form->addTextArea('perex')
                ->getControlPrototype()->class('small');
        $form->addTextArea('content');
        $form->addSubmit('send', 'Uložit')
                ->getControlPrototype()->class('flr');
        $form->onSuccess[] = array($this, 'submitArticleEdit');
        return $form;
    }

    public function submitArticleEdit($form) {
        if ($form['send']->isSubmittedBy()) {
            $id = $this->getParam('id');
            $section = $this->getParam('section');
            $values = $form->getValues();
            if ($values['description'] == '') {
                $values['description'] = $values['title']; // TODO - generate from content
            }
            $values['date'] = $this->dateToDB($values['date']);
            $articlesModel = $this->context->createArticles();
            if ($id) {
                $articlesModel->update($id, $values);
                $this->flashMessage('Článek byl změněn', 'positive');
            } else {
                $values['seoname'] = Strings::webalize($values['title']);
                $values['section'] = $section;
                $articlesModel->insert($values);
                $this->flashMessage('Článek byl uložen', 'positive');
            }
        }
        $this->redirect('default', array('section' => $section));
    }

    public function handleDelete($id) {
        $articlesModel = $this->context->createArticles();
//        try {
            $articlesModel->delete($id);
            $this->flashMessage('Článek byl smazán.', 'positive');
//        } catch (Exception $e) {
//            $this->flashMessage('Článek se nepodařilo smazat.', 'negative');
//        }
        $this->redirect('this');
    }

}
