<?php

namespace AdminModule;

use Indil\Form;

class PagePresenter extends BasePresenter {

    public function renderDefault($section, $seoname) {
        $pageModel = $this->context->createPage();
        $page = $pageModel->find($section, $seoname);
        $this->template->page = $page;

//        if ($row) {
//            $this->template->page = $row;
//        } else {
//            $this->redirect('edit', $seoname);
//        }
    }

    public function actionEdit($id) {
        $pageModel = $this->context->createPage();
        $page = $pageModel->get($id);
        if ($page) {
            $this['pageEdit']->setDefaults($page);
        }
        $this->template->form = $this['pageEdit'];
        $this->template->seoname = $page->seoname;
    }
    
    protected function createComponentPageEdit() {
        $form = new Form;
        $form->addText('title', 'Nadpis')
                ->addRule(Form::FILLED, 'Vyplňte nadpis stránky');
        $form->addText('description', 'Popis stránky:')
                ->getControlPrototype()->class('longer')->maxLength(160);
        $form->addTextArea('content');
        $form->addSubmit('send', 'Uložit')
                ->getControlPrototype()->class('flr');
        $form->onSuccess[] = array($this, 'submitPageEdit');
        return $form;
    }

    public function submitPageEdit($form) {
        $id = $this->getParam('id');
        $pageModel = $this->context->createPage();
        if ($form['send']->isSubmittedBy()) {
            $values = $form->getValues();
            if ($values['description'] == '') {
                $values['description'] = $values['title']; // TODO - generate from content
            }
            if ($pageModel->isInsertedId($id)) {
//                try {
                    $pageModel->update($id, $values);
                    $this->flashMessage('Stránka byla změněna', 'success');
//                } catch (Exception $e) {
//                    
//                }
            } else {
//                try {
                    $values['seoname'] = $seoname;
                    $values['date%sql'] = 'NOW()';
                    $page->insert($values);
                    $this->flashMessage('Stránka byla uložena', 'positive');
//                } catch (Exception $e) {
//                    
//                }
            }
        }
        $page = $pageModel->get($id);
        $this->redirect('default', array('section' => $page->section, 'seoname' => $page->seoname));
    }

}